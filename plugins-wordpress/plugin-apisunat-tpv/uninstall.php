<?php
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
$table_name = $wpdb->prefix . "lars_pos_keys";
$table_categories = $wpdb->prefix . "lars_pos_categories";
$table_products = $wpdb->prefix . "lars_pos_products";
$sql = "DROP TABLE IF EXISTS {$table_name};";
$wpdb->query( $sql );
$sql = "DROP TABLE IF EXISTS {$table_categories};";
$wpdb->query( $sql );
$sql = "DROP TABLE IF EXISTS {$table_products};";
$wpdb->query( $sql );
if( wp_next_scheduled( 'lars_pos_cron_hook' ) ) {
	wp_clear_scheduled_hook( 'lars_pos_cron_hook' );
}
if( ! wp_next_scheduled( 'lars_pos_cron_hook_stock' ) ) {
	wp_clear_scheduled_hook( 'lars_pos_cron_hook_stock' );
}