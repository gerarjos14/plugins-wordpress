<?php
/**
 * FUNCIONES DE CONSULTA A LA BASE DE DATOS
 */



function dbGetProductsOrderDate($wpdb){
    $query = "SELECT * FROM {$wpdb->prefix}".TABLE_PRODUCTS." ORDER BY updated_at ASC LIMIT 80;";
    return $wpdb->get_results( $query );
}

/**
 * FUNCIONES PARA CREAR DATOS EN WOOCOMMERCE
 */
function wcStoreProducts($woocommerce, $data){
    return $woocommerce->post('products/batch', $data);
}
function wcStoreTerms($woocommerce, $id, $name ){
    $data = [ 'name' => $name ];
    return $woocommerce->post("products/attributes/{$id}/terms", $data);
}
function wcGetTerms($woocommerce, $id, $page){
    return $woocommerce->get("products/attributes/{$id}/terms", ['page' => $page , 'per_page' => '100']);
}

function arrayProductData($product, $apiProduct, $woocommerce, $attr, &$terms){
    $options = array();
    $variations = array();
    $price = resolvePrice($product->precio_inc_igv, $product->increase, $product->type_increase);
    
    $stock_total = 0;
    $variation = array();
    if(!empty($product->variations)){
        $dbVariations = explode(',', $product->variations);       
        foreach($dbVariations as $dbVar){
            $name_and_id = explode(':', $dbVar);
            $variation[strtolower($name_and_id[1])] = [
                'id' => $name_and_id[0],
                'name' => $name_and_id[1],
            ];
        }
    }

    
    foreach ($apiProduct as $value){
        //write_log('functions-stock line 49');
        //write_log($value);
        $stock = $value['CountQTY'][0]['qte']; //cantiddad en stock
        if(!($stock > 0)){
            $stock = 0;
        }

        // Si el producto es de lumise es simple
        // REVISAR!
        // revisar cómo se puede hacer con variaciones 
        // definir con qué rango son las variaciones, ejemplo -> color.
       

        $product->lumise = true; 
        if($product->lumise){
            $stock_total = $stock_total + $stock;
        }else{
            $indice = strtolower($value['color']);
            if(!isset($terms[$indice])){            
                $term = wcStoreTerms($woocommerce, $attr, $value['color']);
                $terms[$indice] = [
                    'id' => $term->id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                ];
            }
            array_push($options, $value['color']);
            if(count($variation) && isset($variation[$indice])){
                $variations['update'][] = [
                    'id'             => $variation[$indice]['id'],
                    'regular_price'  => strval($price), 
                    'stock_quantity' => $stock, 
                    'stock_status'   => ($stock > 0) ? 'instock' : 'outofstock',                
                ];
            }else{
                $variations['create'][] = [
                    'regular_price'  => strval($price),                
                    'manage_stock'   => true,
                    'stock_quantity' => $stock, 
                    'stock_status'   => ($stock > 0) ? 'instock' : 'outofstock',
                    'attributes'     => [
                        [
                            'id'             => $attr,
                            'option'         => $terms[$indice]['name'],
                        ],
                    ], 
                ];
            }
        }
    }
    

    if(!is_null(API_PATH_IMAGES)){
        $data = [
            'api_product_id' => $product->api_product_id,
            'w_product_id'   => $product->w_product_id,        
            'name'           => $product->nombre,
            'sku'            => $product->SKU_code,
            'description'    => $product->descripcion,            
            //'image'          => $src_image,        
            'w_variaciones'  => $variations,
            'categories' => explode(',',substr($product->categories, 1, -1)),
        ];
        $num = 0;
        foreach($value['images'] as $img){
            $data['image'][$num] = ['src' => API_PATH_IMAGES . $img];
            $num++;
            
        }   
        //$data['image'] = $images;

    }else{
        $data = [
            'api_product_id' => $product->api_product_id,
            'w_product_id'   => $product->w_product_id,        
            'name'           => $product->nombre,
            'sku'            => $product->SKU_code,
            'description'    => $product->descripcion,        
            'w_variaciones'  => $variations,
            'categories' => explode(',',substr($product->categories, 1, -1)),
        ];
    }

    if($product->lumise){
        $data['regular_price'] = strval($price);
        $data['stock_quantity'] = $stock_total;
    }else{
        $data['attributes'] = [
            [
                'id' => $attr,
                'visible' => true,
                'variation' => true,
                'options' => $options,
            ],
        ];
    }
    return $data;
}

function arrayCreateProducts($products){
    $response = array();    
    $array_products = array_chunk($products, 100);
    for ($i=0; $i < count($array_products); $i++) { 
        $acc = array();        
        $create = array();        
        $update = array();        
        foreach ($array_products[$i] as $product) {
            // write_log('error woocommerce');
            // write_log($product);
            if(($product['w_product_id'] != 0) ){
                $data = [
                    'id'             => $product['w_product_id'], 
                    'name'           => $product['name'],
                    'sku'            => $product['sku'],
                    'description'    => $product['description'],
                    'categories'     => dbGetCategoriesById($product['categories']),
                 //   'images'         => [  $product['image'] ],
                    'api_product_id' => $product['api_product_id'], 
                    'w_variaciones'  => $product['w_variaciones'], 
                ];
                foreach($product['image'] as $img){
                    $data['images'][]  = $img;
                }
                if(isset($product['attributes'])){
                    $data['attributes'] = $product['attributes'];
                }else{
                    $data['stock_status'] = ($product['stock_quantity'] > 0) ? 'instock' : 'outofstock';
                    $data['stock_quantity'] = $product['stock_quantity'];
                    $data['regular_price'] = strval($product['regular_price']);
                }

                $update[] = $data;
            }else{
                $data = [
                    'name'           => $product['name'],
                    'description'    => $product['description'],
                    'categories'     => dbGetCategoriesById($product['categories']),
                    //'images'         => [$product['image']],
                    'api_product_id' => $product['api_product_id'], 
                    'w_variaciones'  => $product['w_variaciones'], 
                    'sku'            => $product['sku'],
                ];

                foreach($product['image'] as $img){
                    $data['images'][]  = $img;
                }

                if(isset($product['attributes'])){
                    $data['type'] = 'variable';
                    $data['attributes'] = $product['attributes'];
                }else{
                    $data['type'] = 'simple';
                    $data['manage_stock'] = true;
                    $data['stock_status'] = ($product['stock_quantity'] > 0) ? 'instock' : 'outofstock';
                    $data['stock_quantity'] = $product['stock_quantity'];
                    $data['regular_price'] = strval($product['regular_price']);
                }
                $create[] = $data;
            }
        }
        if (count($create)) $acc['create'] = $create;
        if (count($update)) $acc['update'] = $update;        
        if (count($acc)) $response[] = $acc;
    }
    return $response;
}

function arrayCreateVartiations($array){
    $response = array();
    foreach ($array as $value) {
        for ($i=0; $i < count($value[0]); $i++) {  
            $id = $value[0][$i]->id;           
            $var = $value[1][$i]['w_variaciones'];
            $response[$id][] = $var;
        }        
    }
    return $response;
}

function dbArrayCreateProducts($array, $variants){
    $response = array();
    foreach ($array as $value) {
        for ($i=0; $i < count($value[0]); $i++) { 
            $id = $value[0][$i]->id;
            $var = $variants[$id];
            $response[] = [ 
                'w_product_id'   => $id,
                'api_product_id' => $value[1][$i]['api_product_id'],
                'variations'     => $var,
            ];
        }        
    }
    return $response;
}


function lastUpdatedRecords($dbPoducts){
    $response= array();
    foreach ($dbPoducts as $product){
        $response[] = [
            'id'         => $product->id,
            'updated_at' => current_time('Y-m-d H:i:s'),
        ];
    }
    return $response;
}

/**
 * Función updateStockAPI
 * @author
 * @param array $data
 * Actualización de stock del producto al sistema POS.
 */
function updateStockAPI($data){
    // write_log($data);
    $postdata = http_build_query($data);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, API_URL . 'api/api_categories/products/update_stock');
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    // se cierra la conexión cURL
    curl_close($ch);
    if($result){
        return 1; // actualización exitosa
    }else{
        return $result; // error
    }
}

/**
 * Función createSaleAPI
 * @author
 * @param array $data
 * Se envían todos los datos de la compra al sistema para generar allí la venta
 * cuyo origen será 'Wordpress'.
 */
function createSaleAPI($data){
    $postdata = http_build_query($data);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, API_URL . 'api/api_categories/sales/create');
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    // se cierra la conexión cURL
    curl_close($ch);
    if(!empty($result)){
        write_log('Ventas generadas con éxito!');
        write_order_notes('Ventas generadas con éxito en el sistema POS', $data['id_order_w']);
    }else{
        write_log('Error al generar ventas en POS ' . $result);
        write_order_notes('Error al generar ventas en el sistema POS'. $result, $data['id_order_w']);
    }
}

/**
 * Función updateStatusAPI
 * @author
 * @param array data
 * Se manda id de venta y status en array al sistema.
 */
function updateStatusAPI($data){
    $postdata = http_build_query($data);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, API_URL . 'api/api_categories/sales/status/update');
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    // se cierra la conexión cURL
    curl_close($ch);
    
    if($result){
        write_log('Venta actualizada con éxito en POS');
        write_order_notes('Venta actualizada con éxito en el sistema POS', $data['id_venta_w']);
    }else{
        write_log('Error al actualizar la venta en POS' . $result);
        write_order_notes('Error al actualizar la venta en el sistema POS', $data['id_venta_w']);
    }
}

/**
 * Función sendInvoiceToClient
 * @author
 * Se envían los datos de la orden al sistema.
 * Se envía la boleta correspondiente al cliente, en función del email
 * que ingresó en checkout.
 */
function  sendInvoiceToClient($order_data){
    
    // se filtran y envían solo los datos que nos interesan
    // id de la orden
    // método de facturación
    $data_order = [
        'id' => $order_data['id'],
        'tipo_facturacion' => $order_data['meta_data'][4]->value == 13 ? 'Factura' : 'Boleta',
    ];

    // se define la url endpoint para envíar la factura
    $url = API_URL . 'api/api_categories/sales/send/invoice/' . $data_order['id'] . '/' . $data_order['tipo_facturacion'] . '';

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    $result = curl_exec($ch);
    
    // se cierra la conexión cURL
    curl_close($ch);
    if($result == 1){
        write_log('Se envió la factura al cliente con éxito');
        write_order_notes(
            'Se envío la '. $data_order['tipo_facturacion'] .' al cliente '. $order_data['billing']['first_name']. ' '. $order_data['billing']['last_name'] .' con éxito',
            $order_data['id']
        );        
        // envio data sunat
        sendDataSunat($order_data);
        sendDataRemissionSunat($order_data);
        // envio data Guia remitente SUNAT
        
    }else{
        //write_log($result);
        write_log('Error al enviar la factura al cliente');
        write_order_notes('Error al enviar la '. $data_order['tipo_facturacion'] .' al cliente', $order_data['id']);
    }
}

/**
 * Función sendDataSunat
 * @author
 * Se envian los datos correspondiente a la SUNAT para poder realizar la facturación correspondiente
 */
function sendDataSunat($order_data){

    global $wpdb;

    // busco los valores que se cargaron en la configuracion de la venta
    $query = "SELECT * FROM {$wpdb->prefix}lars_pos_config_sales ORDER BY id ASC LIMIT 1;";
    $result = $wpdb->get_row( $query );
    $data_order = [
        'id'                       => $order_data['id'],
        'tipo_facturacion'         => $order_data['meta_data'][4]->value == 13 ? 'factura' : 'boleta',
        'tax_category_id'          => $result->impuesto,
        'type_invoice_code_number' => $result->factura,
        'ruc'                      => $order_data['meta_data'][5]->value == -1 ? null: $order_data['meta_data'][5]->value,
    ];

    $postdata = http_build_query($data_order);
     
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, API_URL . 'api/api_categories/sales/sendSunat');
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    write_log($result);

    $data_to_sunat = json_decode($result);
    // if(isset($data_to_sunat['status'])){
    //     if($data_to_sunat['status'] == 'ERROR'){
    //         write_order_notes('Error al enviar SUNAT'. $result, $order_data['id']);
    
    //     }else{
    //         write_order_notes('Se envió correctamente al información del pago a la SUNAT'. $result, $order_data['id']);
    //     }
    // }
}

function sendDataRemissionSunat($order_data){
    global $wpdb;

    // busco los valores que se cargaron en la configuracion de la venta
    $query = "SELECT * FROM {$wpdb->prefix}lars_pos_config_sales ORDER BY id ASC LIMIT 1;";
    $result = $wpdb->get_row( $query );
    $data_order = [
        'id'                            => $order_data['id'],
        'tipo_facturacion'              => $order_data['meta_data'][4]->value == 13 ? 'factura' : 'boleta',
        'tax_category_id'               => $result->impuesto,
        'handling_code'                 => '01', // VENTA
        'type_invoice_code_number'      => $result->factura,
        'GrossWeightMeasureTotal'       => $result->peso_total,
        'SplitConsignmentIndicator'     => $result->trasbordo == 'SI' ? true : false,
        'TransportModeCode'             => $result->modalidad_transporte,
        'identification_transport'      => $result->id_transport,
        'identification_transport_name' => $result->nombre_transportista,
        'OriginAddressName'             => 'LOS OLIVOS', // DEFAULT
        'origin_district_id'            => '150101', // DEFAULT
        'ruc'                           => $order_data['meta_data'][5]->value == -1 ? null: $order_data['meta_data'][5]->value

    ];

    $postdata = http_build_query($data_order);
     
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, API_URL . 'api/api_categories/sales/sendSunatRemision');
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    write_log($result);
}


/**
 * Función getDataWarehouse
 * @author
 * 
 * Se consulta y se trae toda la información de los almacenes 
 * para que el usuario pueda elegir con qué almacen se hará la importación de productos
 * 
 */

 function getDataWarehouse(){
 
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, API_URL . 'api/api_categories/warehouses/list');
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    return json_decode($result);   
 }

 function getDataActualWarehouse($id){


    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, API_URL . 'api/api_categories/warehouses/'.WAREHOUSE.'');
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0); 

    $result = curl_exec($ch);

    return json_decode($result);  
 }