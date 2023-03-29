<?php

/**
 * Archivo para activar eliminación de tablas y demás acciones o archivos 
 * relacionados con el plugin.
 * 
 * Se activa al momento de desintalar el plugin.
 * 
 * @since 1.0.0
 * @package BillConnector
 * 
 * 
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}



/**
 * Agregar todo el código necesario para eliminar en la desintalación del plugin
 * 
 */

 global $wpdb;

 // PROCESO PARA ELIMINAR BD   

    // GENERAL
    $table_gral_config = $wpdb->prefix . "bc_config";
    $bill_config   = "DROP TABLE IF EXISTS  {$table_gral_config} ";
    $wpdb->query($bill_config);

    $table_services = $wpdb->prefix . "bc_services";
    $bill_services = "DROP TABLE IF EXISTS {$table_services} ";
    $wpdb->query($bill_services);

// FIN PROCESO ELIMNAR BD

// PROCESO PARA ELIMINAR CRONS
if( wp_next_scheduled( 'bill_connector_cron_hook_paid_services_review' ) ) {
	wp_clear_scheduled_hook( 'bill_connector_cron_hook_paid_services_review' );
}