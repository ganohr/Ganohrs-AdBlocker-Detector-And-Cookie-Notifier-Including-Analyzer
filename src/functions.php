<?php
if (!defined('ABSPATH')) {
  exit;
}

require_once 'defines.php';

function gnr_aa_countup_accesslimit($need_countup = true) {
	$key = 'gnr-aac-' . @$_SERVER['REMOTE_ADDR'];
	$access_count = gnr_get_transient_real($key);
	if ($access_count === false || $access_count === 0) {
		$access_count = 1;
	}
	if ($access_count >= GNR_ACCESSANALYZ_CALL_LIMIT) {
		gnr_aa_die();
	}
	if ($need_countup) {
		$access_count++;
	}
	if ($access_count >= GNR_ACCESSANALYZ_CALL_LIMIT) {
		gnr_set_transient_real($key, $access_count, GNR_ACCESSANALYZ_BLOCK_TIME);
		gnr_aa_die();
	}
	gnr_set_transient_real($key, $access_count, GNR_ACCESSANALYZ_CALL_TIMEOUT);
}

function gnr_build_transient_query($table, $timeout, $key) {
	global $wpdb;
	$option_name = "_transient_$timeout$key";
	return $wpdb->prepare("
		SELECT
			option_id,
			option_value
		FROM
			$table
		WHERE
			option_name = '%s'
		", $option_name
	);
}

function gnr_get_transient_real($key) {
	if (!wp_using_ext_object_cache()) {
		return get_transient($key);
	}

	global $wpdb;
	$table = $wpdb->prefix . 'options';
	$res_data = $wpdb->get_row(gnr_build_transient_query($table, '', $key), ARRAY_A);
	$res_timeout = $wpdb->get_row(gnr_build_transient_query($table, 'timeout_', $key), ARRAY_A);

	if ($res_data === null) {
		if ($res_timeout !== null) {
			$wpdb->delete($table, array('option_id' => $res_timeout['option_id']));
		}
		if (wp_using_ext_object_cache()) {
			delete_transient($key);
		}
		return false;
	}
	$now = time();
	$expires = @$res_timeout['option_value'];
	if ($expires != null
		&& is_numeric($expires)
		&& $expires < $now
	) {
		$wpdb->delete($table, array('option_id' => $res_data['option_id']));
		$wpdb->delete($table, array('option_id' => $res_timeout['option_id']));
		if (wp_using_ext_object_cache()) {
			delete_transient($key);
		}
		return false;
	} elseif ($expires === null) {
		$expires = 0;
	}

	$obj = unserialize($res_data['option_value']);
	if (wp_using_ext_object_cache()) {
		set_transient($key, $obj, $expires);
	}

	return $obj;
}

function gnr_set_transient_real($key, $value, $expires = 0) {
	if (!wp_using_ext_object_cache()) {
		return set_transient($key, $value, $expires);
	}

	global $wpdb;
	$table = $wpdb->prefix . 'options';
	$res_data = $wpdb->get_row(gnr_build_transient_query($table, '', $key), ARRAY_A);
	$res_timeout = $wpdb->get_row(gnr_build_transient_query($table, 'timeout_', $key), ARRAY_A);

	$now = time();
	$data = serialize($value);

	$result = false;
	if ($res_data !== null) {
		$result = $wpdb->update($table, array('option_name' => "_transient_$key", 'option_value' => $data), array('option_id' => $res_data['option_id']));
	} else {
		$result = $wpdb->insert($table, array('option_name' => "_transient_$key", 'option_value' => $data));
	}

	if ($res_timeout !== null) {
		if ($expires === 0) {
			$wpdb->delete($table, array('option_id' => $res_timeout['option_id']));
		} else {
			$wpdb->update($table, array('option_name' => "_transient_timeout_$key", 'option_value' => ($now + $expires)), array('option_id' => $res_timeout['option_id']));
		}
	} elseif ($expires !== 0) {
		$wpdb->insert($table, array('option_name' => "_transient_timeout_$key", 'option_value' => ($now + $expires)));
	}

	if (wp_using_ext_object_cache()) {
		set_transient($key, $value, $expires);
	}
	return $result;
}

function gnr_delete_transient_real($key) {
	if (!wp_using_ext_object_cache()) {
		return delete_transient($key);
	}

	global $wpdb;
	$table = $wpdb->prefix . 'options';
	$res_data = $wpdb->get_row(gnr_build_transient_query($table, '', $key), ARRAY_A);

	$result = false;
	if ($res_data !== null) {
		$result = $wpdb->delete($table, array('option_id' => $res_data['option_id']));
	}
	$res_timeout = $wpdb->get_row(gnr_build_transient_query($table, 'timeout_', $key), ARRAY_A);
	if ($res_timeout !== null) {
		$wpdb->delete($table, array('option_id' => $res_timeout['option_id']));
	}

	if (wp_using_ext_object_cache()) {
		delete_transient($key);
	}

	return $result;
}

function gnr_aa_output_access_log($verified) {
	$ip    = gnr_aa_get_ip();
	$token = gnr_aa_get_token();
	$ref   = gnr_aa_get_ref();
	$ua    = gnr_aa_get_ua();
	$data  = gnr_aa_get_data_text();
	$nonce = gnr_aa_restore_nonce();
	wo_log( "GNR-ACCESS-ANALIZER     ip = [$ip], token = [$token], data=[$data], verified=[$verified], ref = [$ref], ua = [$ua], nonce=[$nonce]" );
}

function gnr_aa_create_store_data() {
	return array(
		'token' => gnr_aa_get_token(),
		'ua' => gnr_aa_get_ua(),
		'ref' => gnr_aa_get_ref(),
		'data' => gnr_aa_get_data()
	);
}

function gnr_aa_md5($str, $tries = 3) {
	for ($i = 0; $i < $tries; $i++) {
		$str = bin2hex(hash("md5", $str, true));
	}
	return $str;
}

function gnr_aa_get_token() {
	$base = gnr_aa_md5(@$_SERVER['REMOTE_ADDR'], GNR_ACCESSANALYZ_TOKEN_RAINBOW);
	$key  = GNR_ACCESSANALYZ_TOKEN_SALTNAME . $base;
	$salt = gnr_get_transient_real($key);
	if (false === $salt) {
		$salt = gnr_aa_md5(bin2hex(random_bytes(GNR_ACCESSANALYZ_TOKEN_SALTBYTES)), 1);
	}
	gnr_set_transient_real($key, $salt, GNR_ACCESSANALYZ_TOKEN_SALTTIMEOUT);

	return gnr_aa_md5($base . $salt);
}

function gnr_aa_get_ip() {
	return @$_SERVER['REMOTE_ADDR'];
}


function gnr_aa_get_ref() {
	return @$_SERVER['HTTP_REFERER'];
}

function gnr_aa_get_ua() {
	return @$_SERVER['HTTP_USER_AGENT'];
}

function gnr_aa_get_data() {
	return mb_substr(@$_POST['data'], 0, 200);
}

function gnr_aa_get_data_text() {
	$data = gnr_aa_get_data();
	$data_map = array(
		'1'  => 'GNR_ACCESSANALYZ_DATA_ADB_OPEN' 		,
		'2'  => 'GNR_ACCESSANALYZ_DATA_ADB_SKIP' 		,
		'3'  => 'GNR_ACCESSANALYZ_DATA_ADB_CLOSE_BUTTON',
		'4'  => 'GNR_ACCESSANALYZ_DATA_ADB_CLOSE_OUTER'	,
		'5'  => 'GNR_ACCESSANALYZ_DATA_ADB_MOVE' 		,
		'6'  => 'GNR_ACCESSANALYZ_DATA_ADB_AGREE_ADB'	,
		'7'  => 'GNR_ACCESSANALYZ_DATA_COOKIE_OPEN'		,
		'8'  => 'GNR_ACCESSANALYZ_DATA_COOKIE_ACCEPT'	,
		'9'  => 'GNR_ACCESSANALYZ_DATA_COOKIE_MOVE'		,
		'10' => 'GNR_ACCESSANALYZ_DATA_CONTINUE_VIEWING',
		'11' => 'GNR_ACCESSANALYZ_DATA_ADB_CONTINUE_VIEWING',
	);
	if (array_key_exists($data, $data_map)) {
		return $data_map[$data];
	}
	return "unknown_data($data)";
}

function gnr_aa_get_nonce_key() {
	return GNR_ACCESSANALYZ_NONCE_ACTION . '-' . gnr_aa_get_token();
}

function gnr_aa_create_nonce() {
	return wp_create_nonce( gnr_aa_get_nonce_key() );
}

function gnr_aa_store_nonce() {
	$key = 'gnr-aat-' . gnr_aa_get_token();
	return gnr_set_transient_real($key, gnr_aa_create_nonce(), );
}

function gnr_aa_restore_nonce() {
	$key = 'gnr-aat-' . gnr_aa_get_token();
	return gnr_get_transient_real($key);
}

function gnr_aa_check_data() {
	$data = gnr_aa_get_data();
	return (
		   GNR_ACCESSANALYZ_DATA_ADB_OPEN === $data
		|| GNR_ACCESSANALYZ_DATA_ADB_SKIP === $data
		|| GNR_ACCESSANALYZ_DATA_ADB_CLOSE_BUTTON === $data
		|| GNR_ACCESSANALYZ_DATA_ADB_CLOSE_OUTER === $data
		|| GNR_ACCESSANALYZ_DATA_ADB_MOVE === $data
		|| GNR_ACCESSANALYZ_DATA_ADB_AGREE_ADB === $data
		|| GNR_ACCESSANALYZ_DATA_COOKIE_OPEN === $data
		|| GNR_ACCESSANALYZ_DATA_COOKIE_ACCEPT === $data
		|| GNR_ACCESSANALYZ_DATA_COOKIE_MOVE === $data
		|| GNR_ACCESSANALYZ_DATA_CONTINUE_VIEWING === $data
		|| GNR_ACCESSANALYZ_DATA_ADB_CONTINUE_VIEWING === $data
	);
}

function gnr_aa_verify_nonce() {
	$ref = gnr_aa_get_ref();
	return $ref != null
		&& $ref !== ''
		&& (
			   strpos($ref, 'https://ganohr.net/' ) === 0
			|| strpos($ref, 'https://ganohr-net.translate.goog/' ) === 0
		)
		&& gnr_aa_check_data()
		&& wp_verify_nonce( gnr_aa_restore_nonce(), gnr_aa_get_nonce_key() );
}

function gnr_aa_die() {
	header('HTTP/1.1 403 Forbidden');
	die();
}

function gnr_aa_enqueue_scripts() {
	wp_enqueue_script( 'js-cookie', 'https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js', array(), NULL, true );
	wp_enqueue_script( 'gnr-access-analyz', 'https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js', array('js-cookie', 'jquery'), NULL, true );
	wp_enqueue_style( 'gnr-adb-modal-style', site_url() . '/gnr-access-analyze/gnr-adb-modal.css'
	wp_enqueue_style( 'gnr-cookie-modal-style', site_url() . '/gnr-access-analyze/gnr-cookie-modal.css'
}

function gnr_aa_append_script($content) {
	if (!function_exists('gnr_is_amp')
		|| gnr_is_amp()
		|| !function_exists('gnr_isGoogleBot')
		|| gnr_isGoogleBot($ip)) {
		return $content;
	}

	// 既にスクリプト設置済みなら抜ける
	if (strpos($content, 'gnr-cookie-modal') !== false) {
		return $content;
	}
	gnr_aa_countup_accesslimit( false );

	// nonceを生成し、記憶する
	gnr_aa_store_nonce();

	require_once site_url() . "/gnr-access-analyze/template-adb-modal.php";
	require_once site_url() . "/gnr-access-analyze/template-cookie-modal.php";

	return $content;
}
add_filter( 'the_content', 'gnr_aa_append_script', 1);
