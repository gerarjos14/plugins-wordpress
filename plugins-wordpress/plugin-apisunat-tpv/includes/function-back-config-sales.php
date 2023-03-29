<?php
require_once plugin_dir_path(__FILE__) . '/helper.php';

/**
 * Funciones para la configuración de las ventas
 * 
 */

function getData(){
   global $wpdb;

   // busco el valor que se haya cargado antes
   $query = "SELECT * FROM {$wpdb->prefix}lars_pos_config_sales ORDER BY id ASC LIMIT 1;";
   $result = $wpdb->get_row( $query );
   return $result;
}

 function getFacturas(){
   global $wpdb;
   $result = getData();
   $query_values = "SELECT * FROM {$wpdb->prefix}lars_pos_config_sales_values WHERE categorie = 'factura'";
   $resulta_facturas = $wpdb->get_results($query_values);

   foreach($resulta_facturas as $key){
      $data_facturas[] = [
         'label' => $key->label,
         'code' => $key->code,
         'default' => $key->code == $result->factura ? '1' : '0'
      ];
   } 

   return $data_facturas;
 }

 function getImpuestos(){
   global $wpdb;
   $result = getData();

   $query_values = "SELECT * FROM {$wpdb->prefix}lars_pos_config_sales_values WHERE categorie = 'impuesto'";
   $resultados_impuestos = $wpdb->get_results($query_values);

   foreach($resultados_impuestos as $key){
      $data_impuestos[] = [
         'label' => $key->label,
         'code' => $key->code,
         'default' => $key->code == $result->factura ? '1' : '0'
      ];
   } 
   return $data_impuestos;
 }

 function getMotivo(){
   global $wpdb;
   $result = getData();
   $query_values = "SELECT * FROM {$wpdb->prefix}lars_pos_config_sales_values WHERE categorie = 'motivo_traslado'";
   $resultados_motivo_traslado = $wpdb->get_results($query_values);

   foreach($resultados_motivo_traslado as $key){
      $data_motivo[] = [
         'label' => $key->label,
         'code' => $key->code,
         'default' => $key->code == $result->factura ? '1' : '0'
      ];
   }

   return $data_motivo;
 }

 function getModalidad(){
   global $wpdb;
   $result = getData();
   $query_values = "SELECT * FROM {$wpdb->prefix}lars_pos_config_sales_values WHERE categorie = 'modalidad_transporte'";
   $resultados_modalidad_transporte = $wpdb->get_results($query_values);

   foreach($resultados_modalidad_transporte as $key){
      $data_modalidad[] = [
         'label' => $key->label,
         'code' => $key->code,
         'default' => $key->code == $result->factura ? '1' : '0'
      ];
   }
   return $data_modalidad;
 }
 