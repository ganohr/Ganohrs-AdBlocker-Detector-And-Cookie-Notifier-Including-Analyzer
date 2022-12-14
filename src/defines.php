<?php
if (!defined('ABSPATH')) {
  exit;
}
if (!defined('GNR_ACCESSANALYZ_NONCE_FIELD')) {
	define('GNR_ACCESSANALYZ_NONCE_FIELD', 'gnr-accessanalyz-nonce');
}
if (!defined('GNR_ACCESSANALYZ_NONCE_ACTION')) {
	define('GNR_ACCESSANALYZ_NONCE_ACTION', 'gnr-accessanalyz-nonce-action');
}
if (!defined('GNR_ACCESSANALYZ_TOKEN_RAINBOW')) {
	define('GNR_ACCESSANALYZ_TOKEN_RAINBOW', 3);
}
if (!defined('GNR_ACCESSANALYZ_TOKEN_SALTNAME')) {
	define('GNR_ACCESSANALYZ_TOKEN_SALTNAME', 'gnr-aas-');
}
if (!defined('GNR_ACCESSANALYZ_TOKEN_SALTBYTES')) {
	define('GNR_ACCESSANALYZ_TOKEN_SALTBYTES', 5);
}
if (!defined('GNR_ACCESSANALYZ_TOKEN_SALTTIMEOUT')) {
	define('GNR_ACCESSANALYZ_TOKEN_SALTTIMEOUT', HOUR_IN_SECONDS);
}
if (!defined('GNR_ACCESSANALYZ_CALL_LIMIT')) {
	define('GNR_ACCESSANALYZ_CALL_LIMIT', 50);
}
if (!defined('GNR_ACCESSANALYZ_CALL_TIMEOUT')) {
	define('GNR_ACCESSANALYZ_CALL_TIMEOUT', MINUTE_IN_SECONDS);
}
if (!defined('GNR_ACCESSANALYZ_BLOCK_TIME')) {
	define('GNR_ACCESSANALYZ_BLOCK_TIME', DAY_IN_SECONDS);
}
if (!defined('GNR_ACCESSANALYZ_TABLE')) {
	define('GNR_ACCESSANALYZ_TABLE', 'gnr_access_analyze');
}
if (!defined('GNR_ACCESSANALYZ_DATA_ADB_OPEN')) {
	define('GNR_ACCESSANALYZ_DATA_ADB_OPEN', '1');
}
if (!defined('GNR_ACCESSANALYZ_DATA_ADB_SKIP')) {
	define('GNR_ACCESSANALYZ_DATA_ADB_SKIP', '2');
}
if (!defined('GNR_ACCESSANALYZ_DATA_ADB_CLOSE_BUTTON')) {
	define('GNR_ACCESSANALYZ_DATA_ADB_CLOSE_BUTTON', '3');
}
if (!defined('GNR_ACCESSANALYZ_DATA_ADB_CLOSE_OUTER')) {
	define('GNR_ACCESSANALYZ_DATA_ADB_CLOSE_OUTER', '4');
}
if (!defined('GNR_ACCESSANALYZ_DATA_ADB_MOVE')) {
	define('GNR_ACCESSANALYZ_DATA_ADB_MOVE', '5');
}
if (!defined('GNR_ACCESSANALYZ_DATA_ADB_AGREE_ADB')) {
	define('GNR_ACCESSANALYZ_DATA_ADB_AGREE_ADB', '6');
}
if (!defined('GNR_ACCESSANALYZ_DATA_COOKIE_OPEN')) {
	define('GNR_ACCESSANALYZ_DATA_COOKIE_OPEN', '7');
}
if (!defined('GNR_ACCESSANALYZ_DATA_COOKIE_ACCEPT')) {
	define('GNR_ACCESSANALYZ_DATA_COOKIE_ACCEPT', '8');
}
if (!defined('GNR_ACCESSANALYZ_DATA_COOKIE_MOVE')) {
	define('GNR_ACCESSANALYZ_DATA_COOKIE_MOVE', '9');
}
if (!defined('GNR_ACCESSANALYZ_DATA_CONTINUE_VIEWING')) {
	define('GNR_ACCESSANALYZ_DATA_CONTINUE_VIEWING', '10');
}
if (!defined('GNR_ACCESSANALYZ_DATA_ADB_CONTINUE_VIEWING')) {
	define('GNR_ACCESSANALYZ_DATA_ADB_CONTINUE_VIEWING', '11');
}
