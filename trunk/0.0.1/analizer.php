<?php
/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define( 'WP_USE_THEMES', false );

/** Loads the WordPress Environment and Template */
require __DIR__ . '/../wp-blog-header.php';

require_once ABSPATH . '/gnr-access-analyze/functions.php';

gnr_aa_countup_accesslimit( true );
$gnr_aa_verified = gnr_aa_verify_nonce();
gnr_aa_output_access_log( $gnr_aa_verified );

if ( ! $gnr_aa_verified ) {
	gnr_aa_die();
}

global $wpdb;

$table = $wpdb->prefix . GNR_ACCESSANALYZ_TABLE;
$wpdb->insert( $table, gnr_aa_create_store_data() );
