<?php

/*------------------------------- USO API WOOCOMMERCE ---------------------------------------------- */
use Automattic\WooCommerce\Client;
/*-------------------------------------------------------------------------------------------------- */
require_once plugin_dir_path(__FILE__) . '/helper.php';
require_once plugin_dir_path(__FILE__) . '/functions-stock.php';
require_once plugin_dir_path(__FILE__) . '/function-sales.php';


function process_lars_pos_stock() {
    global $wpdb;
    $keys = getKeys($wpdb);
    if($keys){        
        write_log("Comenzo stock");
        $woocommerce = new Client(
            $keys->website, 
            $keys->consumer_key, 
            $keys->consumer_secret,             
            [ 'wp_api'=> true, 'version' => 'wc/v3', 'verify_ssl' => false]		
        );
        if(isset($keys->attr)){    
            $attr = $keys->attr;  
            $terms = array();
            $control = true;
            $page = 1;
            while($control){
                $wc_terms = wcGetTerms($woocommerce, $attr, $page);
                if(count($wc_terms)){ 
                    foreach($wc_terms as $term){
                        $indice = strtolower($term->name);
                        $terms[$indice] = [
                            'id' => $term->id,
                            'name' => $term->name,
                            'slug' => $term->slug,
                        ];
                    }
                    if(count($wc_terms) < 100){ $control = false; }
                }else{ $control = false; }
                $page++;
            }
            
            $dbPoducts = dbGetProductsOrderDate($wpdb);
            if($dbPoducts){
                $lastRecords = lastUpdatedRecords($dbPoducts);
                db_insert_rows( $lastRecords, TABLE_PRODUCTS, true, 'id'); 
                
                $productsData = array(); 
                foreach ($dbPoducts as $product){
                    
                    if($product->precio > 0){
                        $apiProduct = apiGetProduct($product->api_product_id);
                        
                        if(!empty($apiProduct)){
                                //Arma un producto que tenga Stock
                                $productData = arrayProductData($product, $apiProduct, $woocommerce, $attr, $terms);                           
                                if(isset($productData)) $productsData[] = $productData;
                                                       
                        }  
                    }

                }        
                $newProducts = array();
                $oldProducts = array();
                // Ya tengo el stock de los productos el precio y demas, lo tengo que mandar a woocommerce        
                if(count($productsData)){
                    //Armo la creacion para woocommerce
                    $arrayProducts = arrayCreateProducts($productsData);
                    
                    foreach ($arrayProducts as $data) {   
                        //Creo y edito las categorias   
                        $wcProducts = wcStoreProducts($woocommerce, $data);
                        // Acumulo si hay creadas
                        if(!empty($wcProducts->create)){
                            $newProducts[] = array($wcProducts->create, $data['create']);
                        }

                        if(!empty($wcProducts->update)){
                            $oldProducts[] = array($wcProducts->update, $data['update']);
                        }
                    }
                };

                $products = [];
                if(count($newProducts)){
                    $variations = arrayCreateVartiations($newProducts);
                    $variants = array();
                    foreach($variations as $key => $variant){
                        $var = '';          
                        if(count($variant[0])) {
                            $wcVariant = wcStoreVariants($woocommerce, $key, $variant[0]);
                            if(!empty($wcVariant->create)){
                                foreach($wcVariant->create as $wcVar){    
                                    if(isset($wcVar->attributes[0])){
                                        $dbVar = $wcVar->id .':'. $wcVar->attributes[0]->option;    
                                        if(strlen($var)){ 
                                            $var .= ','. $dbVar;
                                        }else{
                                            $var = $dbVar;
                                        }
                                    }
                                }
                            }
                            if(!empty($wcVariant->update)){

                                foreach($wcVariant->update as $wcVar){
                                    if(isset($wcVar->attributes[0])){
                                        $dbVar = $wcVar->id .':'. $wcVar->attributes[0]->option;
                                        if(strlen($var)){ 
                                            $var .= ','. $dbVar;
                                        }else{
                                            $var = $dbVar;
                                        }
                                    }
                                }
                            }                                     
                        }         
       
                        $variants[$key] = $var;
                    }
                    // Registro la relacion entre el id del api y de woocommerce 
                    $products = dbArrayCreateProducts($newProducts, $variants);
                    db_insert_rows( $products, TABLE_PRODUCTS, true, 'api_product_id');  
                } 
                $old_products = [];
                if(count($oldProducts)){   
                    $variations = arrayCreateVartiations($oldProducts);
                    $variants = array();
                    foreach($variations as $key => $variant){
                        $var = '';      
                        if(count($variant[0])) {
                            $wcVariant = wcStoreVariants($woocommerce, $key, $variant[0]);
                            if(!empty($wcVariant->create)){
                                foreach($wcVariant->create as $wcVar){
                                    if(isset($wcVar->attributes[0])){
                                        $dbVar = $wcVar->id .':'. $wcVar->attributes[0]->option;
                                        if(strlen($var)){ 
                                            $var .= ','. $dbVar;
                                        }else{
                                            $var = $dbVar;
                                        }
                                    }
                                }
                            }
                            if(!empty($wcVariant->update)){
                                foreach($wcVariant->update as $wcVar){
                                    $dbVar = $wcVar->id .':'. $wcVar->attributes[0]->option;
                                    if(strlen($var)){ 
                                        $var .= ','. $dbVar;
                                    }else{
                                        $var = $dbVar;
                                    }
                                }
                            }                     
                        }     
                        $variants[$key] = $var;
                    }
                    // Registro la relacion entre el id del api y de woocommerce  
                    $old_products = dbArrayCreateProducts($oldProducts, $variants);
                    db_insert_rows( $old_products, TABLE_PRODUCTS, true, 'api_product_id');  
                } 
            }
        }        
        write_log("Finalizo carga de stock");
       
    }
}

function process_lars_pos_update_stock_tpv( $order ) { 
    
    global $wpdb;

    // data de orden.
    $items = $order->get_items();
    $items_ids = array();
    $grand_total = $num_item = 0;
    
    // obtengo información de la compra que se hizo.
    $order_id   = $order->get_id();
    $order      = wc_get_order( $order_id );
    $order_data = $order->get_data();

    foreach ($order->get_items() as $item_key => $item_values){
        // Acceder a las propiedades de datos de los artículos de pedido (en una matriz de valores)
        $item_data = $item_values->get_data();
        $query   = "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = {$item_data['product_id']} AND meta_key = '_stock';";
        $results = $wpdb->get_results( $query );
        
        // busco data del producto del sistema
        $query_id_product_tpv = "SELECT * FROM {$wpdb->prefix}".TABLE_PRODUCTS." WHERE w_product_id = {$item_data['product_id']}";
        $id_product_tpv       = $wpdb->get_results($query_id_product_tpv);
        
        // Array para mandar al sistema descuento de stock. id y stock actual.
        $items_ids = [
            'id_product'   => $id_product_tpv[0]->api_product_id,
            'stock'        => $results[0]->meta_value,
            'id_warehouse' => WAREHOUSE
        ];

        //actualizo stock en sitema TPV.
        if(updateStockAPI($items_ids)){        
            write_log('Actualización de stock exitosa');
            write_order_notes('Actualización de stock exitosa en el sistema POS', $order_id);
        }else{
            write_log('Error al actualizar stock en sistema TPV');            
            write_order_notes('Error al actualizar stock en sistema POS', $order_id);
        }


        //Armo array con detalles de los productos.
        $product = $item_values->get_product();
        $data_products[$num_item] = [
            'code'               => $product->get_sku(),
            'quantity'           => $item_data['quantity'],
            'product_variant_id' => '',
            'discount'           => 0,
            'DiscountNet'        => 0,
            'discount_Method'    => 2,
            'product_id'         => $id_product_tpv[0]->api_product_id,
            'name'               => $item_data['name'],
            'Net_price'          => $id_product_tpv[0]->precio,
            'Unit_price'         => $id_product_tpv[0]->precio,
            'taxe'               => 0,
            'tax_method'         => 1,
            'tax_percent'        => 0,
            'unitSale'           => '',
            'fix_price'          => $id_product_tpv[0]->precio,
            'description'        => $id_product_tpv[0]->descripcion,            
            'subtotal'           => round($id_product_tpv[0]->precio * $item_data['quantity']), // redondeo numero con dos decimales
        ];

        $num_item++;
    }    
    
    arrrayDataSale($data_products, $order_data, $order_id);
}

/** ------- FUNCIONES CAMBIO / ACTUALIZACIÓN DE ESTADO DE VENTA WOOCOOMERCE - SIST. POS ------- **/

function process_lars_pos_sale_on_hold($order){
    $data_update_status = [
        'id_venta_w' => $order,
        'status'    => 'on_hold'
    ];
    updateStatusAPI($data_update_status);
}

function process_lars_pos_sale_on_pending($order){
    $data_update_status = [
        'id_venta_w' => $order,
        'status'    => 'pending'
    ];
    updateStatusAPI($data_update_status);
}



function process_lars_pos_sale_processing($order){

    $info_order = wc_get_order( $order );
    $order_data = $info_order->get_data();

    $data_update_status = [
        'id_venta_w' => $order,
        'status' => 'processing',
        'data_orden' => $order_data,
        'tipo_factura' => $order_data['meta_data'][1]->value == 13 ? 'factura' : 'boleta',
    ];
    updateStatusAPI($data_update_status);
}

function process_lars_pos_sale_completed($order){
    $info_order = wc_get_order( $order );
    $order_data = $info_order->get_data();
    

    $data_update_status = [
        'id_venta_w' => $order,
        'status' => 'completed',
        'data_orden' => $order_data,             
        'tipo_factura' => $order_data['meta_data'][1]->value == 13 ? 'factura' : 'boleta',
    ];
    updateStatusAPI($data_update_status);
   
    // envío factura al cliente
    sendInvoiceToClient($order_data);
}

function process_lars_pos_sale_failed($order){
    $data_update_status = [
        'id_venta_w' => $order,
        'status' => 'failed'
    ];
    updateStatusAPI($data_update_status);
}

function process_lars_pos_sale_cancelled($order){
    
    $data_update_status = [
        'id_venta_w' => $order,
        'status' => 'cancelled'
    ];
    updateStatusAPI($data_update_status);

    global $wpdb;
    // data de orden.
    $items_ids = array();  
    // obtengo información de la compra que se hizo.
    $order = wc_get_order( $order );

    foreach ($order->get_items() as $item_key => $item_values){
        // Acceder a las propiedades de datos de los artículos de pedido (en una matriz de valores)
        $item_data = $item_values->get_data();
        $query   = "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = {$item_data['product_id']} AND meta_key = '_stock';";
        $results = $wpdb->get_results( $query );
        
        // busco data del producto del sistema
        $query_id_product_tpv = "SELECT * FROM {$wpdb->prefix}".TABLE_PRODUCTS." WHERE w_product_id = {$item_data['product_id']}";
        $id_product_tpv       = $wpdb->get_results($query_id_product_tpv);
        
        // Array para mandar al sistema descuento de stock. id y stock actual.
        $items_ids = [
            'id_product'   => $id_product_tpv[0]->api_product_id,
            'stock'        => $results[0]->meta_value + $item_data['quantity'],        
            'id_warehouse' => WAREHOUSE
        ];

        // incremento stock en sitema TPV.
        if(updateStockAPI($items_ids)){        
            write_log('Incrementación de stock exitosa');
            write_order_notes('Incrementación de stock exitosa', $order);
        }else{
            write_log('Error al incrementar stock en sistema TPV');            
            write_order_notes('Error al incrementar stock en sistema TPV', $order);            

        }
    }    
}

function process_lars_pos_sale_refunded($order){
    $data_update_status = [
        'id_venta_w' => $order,
        'status' => 'refunded'
    ];
    updateStatusAPI($data_update_status);
}


add_action( 'woocommerce_reduce_order_stock', 'process_lars_pos_update_stock_tpv' );
add_action('lars_pos_cron_hook_stock', 'process_lars_pos_stock');

// hooks status orders
add_action( 'woocommerce_order_status_on_hold', 'process_lars_pos_sale_on_hold' );
add_action( 'woocommerce_order_status_pending', 'process_lars_pos_sale_pending' );
add_action( 'woocommerce_order_status_processing', 'process_lars_pos_sale_processing' );
add_action( 'woocommerce_order_status_completed', 'process_lars_pos_sale_completed');
add_action( 'woocommerce_order_status_failed', 'process_lars_pos_sale_failed');
add_action( 'woocommerce_order_status_cancelled', 'process_lars_pos_sale_cancelled');
add_action( 'woocommerce_order_status_refunded', 'process_lars_pos_sale_refunded');
