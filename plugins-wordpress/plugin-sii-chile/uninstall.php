<?php

/**
 * Se activa cuando el plugin va a ser desinstalado
 *
 * @link       http://misitioweb.com
 * @since      1.0.0
 *
 * @package    Billconnector
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/*
 * Agregar todo el código necesario
 * para eliminar ( como las bases de datos, limpiar caché,
 * limpiar enlaces permanentes, etc. ) en la desinstalación
 * del plugin
 */

global $wpdb;

$sql = "DROP TABLE IF EXISTS {$wpdb->prefix}bc_config";
$wpdb->query( $sql );

$sql2 = "DROP TABLE IF EXISTS {$wpdb->prefix}bc_orders";
$wpdb->query( $sql2 );

$sql3 = "DROP TABLE IF EXISTS {$wpdb->prefix}bc_logs";
$wpdb->query( $sql3 );
// se elimina el cron de wp
if( wp_next_scheduled( 'bill_connector_cron_hook_send_email' ) ) {
	wp_clear_scheduled_hook( 'bill_connector_cron_hook_send_email' );
}