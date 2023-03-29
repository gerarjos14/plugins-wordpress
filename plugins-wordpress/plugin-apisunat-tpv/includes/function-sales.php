<?php
require_once plugin_dir_path(__FILE__) . '/helper.php';
require_once plugin_dir_path(__FILE__) . '/functions-stock.php';
/**
 * FUNCIONES VENTAS
 */

/**
 * 
 */
function arrrayDataSale($details, $order_data, $id_order){
    // Obtengo información del pedido
    // Info pago
    $payment = [
        'status'           => ($order_data['status'] == 'on-hold') ?  'pending' : $order_data['status'] ,
        'Reglement'        => $order_data['payment_method_title'],
        'received_amount'  => null,
        'tipo_facturacion' => $order_data['meta_data'][4]->value == 13 ? 'factura' : 'boleta',

    ];

    /**
     * Busco los valores de las departamentos, provincias y distritos de acuerdo al id
     */
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->prefix}ubigeo_departamento WHERE idDepa = '{$order_data['meta_data'][0]->value}'";
    $departamento = $wpdb->get_row($query);

    $query = "SELECT * FROM {$wpdb->prefix}ubigeo_provincia WHERE idProv = '{$order_data['meta_data'][1]->value}'";
    $provincia = $wpdb->get_row($query);

    $query = "SELECT * FROM {$wpdb->prefix}ubigeo_distrito WHERE idDist = '{$order_data['meta_data'][2]->value}'";
    $distrito = $wpdb->get_row($query);
    
    //Armo array con información del cliente, en caso de que no esté se creará en el sistema POS
    $client = [
        'code'         => $order_data['meta_data'][5]->value, // envio mismo valor que el ruc
        'ruc'          => $order_data['meta_data'][5]->value,
        'name'         => $order_data['billing']['first_name'] .' '. $order_data['billing']['last_name'],
        'email'        => $order_data['billing']['email'],
        'address'      => $order_data['billing']['address_1'], // real address
        'city'         => $order_data['billing']['city'],
        'state'        => countryCodeToCountry($order_data['billing']['country']),
        'province'     => provinceCode($order_data['billing']['country'], $order_data['billing']['state']), // nombre de provincia
        'phone_1'      => $order_data['billing']['phone'],
        'email'        => $order_data['billing']['email'],
        'departamento' => $departamento->departamento,
        'provincia'    => $provincia->provincia,
        'distrito'     => $distrito->distrito       
    ];

    $query = "SELECT * FROM {$wpdb->prefix}lars_pos_config_sales ORDER BY id ASC LIMIT 1;";
    $result = $wpdb->get_row( $query );



    $remission=  [
        'handling_code'                 => '1',
        'GrossWeightMeasureTotal'       => $result->peso_total,
        'SplitConsignmentIndicator'     => '0',
        'TransportModeCode'             => $result->modalidad_transporte,
        'StartDate'                     => date("Y-m-d"),
        'identification_transport'      => $result->id_transport,
        'identification_transport_name' => $result->nombre_transportista,
        'DeliveryAddressName'           => $order_data['billing']['address_1'],
        'OriginAddressName'             => 'LOS OLIVOS',
        'origin_district_id'            => '150101',
    ];

    $data_sale = [
        'date'            => $order_data['date_created']->date('Y-m-d'),
        'warehouse_id'    => '1',
        'id_order_w'      => $id_order,
        'tipo_facturacion'=> $order_data['meta_data'][4]->value == 13 ? 'factura' : 'boleta',
        'statut'          => $order_data['status'],
        'notes'           => null,
        'tax_rate'        => 0,
        'TaxNet'          => 0,
        'discount'        => $order_data['discount_total'],
        'shipping'        => $order_data['shipping_total'],
        'GrandTotal'      => $order_data['total'],
        
        'details'         => $details,
        'payment'         => $payment,
        'amount'          => 'NaN',
        'received_amount' => 'NaN',
        'change'          => 0.00,
        'info_client'     => $client,
        'remission'       => $remission,
        'id_warehouse'    => WAREHOUSE
    ];
    // envío data a endpoint
    createSaleAPI($data_sale);
}