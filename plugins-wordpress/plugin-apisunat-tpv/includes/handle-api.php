<?php

/*------------------------------- USO API WOOCOMMERCE ---------------------------------------------- */
use Automattic\WooCommerce\Client;
/*-------------------------------------------------------------------------------------------------- */
require_once plugin_dir_path(__FILE__) . '/helper.php';
require_once plugin_dir_path(__FILE__) . '/functions.php';

function process_lars_pos() {
    global $wpdb;
    $keys = getKeys($wpdb);
    if($keys){ 
        write_log("comenzo categorias");
        $woocommerce = new Client(
            $keys->website, 
            $keys->consumer_key, 
            $keys->consumer_secret,             
            [ 'wp_api'=> true, 'version' => 'wc/v3', 'verify_ssl' => false]		
        );        

        if(!isset($keys->attr)){
            $attr = wcCreateAttribute($woocommerce);            
            $result = $wpdb->update("{$wpdb->prefix}lars_pos_keys", [ 'attr' => $attr->id], ['id' => $keys->id]);
        }

        /**
         * COMIENZA LA CREACION DE CATEGORIAS
         */

        // Trae las categorias de la api
        $apiCategories = apiGetCategories();
        
        if(!empty($apiCategories)){
            $dbCategories = dbGetCategories($wpdb);            
            //Verifica si hay categorias ya registradas
            if(count($dbCategories)){
                // Crea un array para crear registros en woocommerce solo los que no estan registrados en la BD
                $arrayCategories = arrayEditCategories($apiCategories, $wpdb);
            }else{
                // Crea un array para crear registros en woocommerce
                $arrayCategories = arrayCreateCategories($apiCategories);
            }
            $newCategories = array();            
            // si no hay categorias simplemente no entra al foreach
            foreach ($arrayCategories as $data) {   
                //Creo las categorias   
                $wcCategories = wcStoreCategories($woocommerce, $data);
                // Acumulo si hay creadas
                if(count($wcCategories->create)){
                    $newCategories[] = array($wcCategories->create, $data['create']);
                }
            }
            if(count($newCategories)){
                // Registro la relacion entre el id del api y de woocommerce  
                db_insert_rows( dbArrayCreateCategories($newCategories), TABLE_CATEGORIES);
            } 
            
            /**
             * COMIENZA LA CREACION DE PRODUCTOS
             */

            $products = array();
            foreach ($apiCategories as $key => $apiCategory) {
                $apiProducts = apiGetProductsByCategory($apiCategory['id']);
                if(empty($apiProducts)){//Si falla intento una vez mas.
                    $apiProducts = apiGetProductsByCategory($apiCategory['id']);
                }
                

                if(!empty($apiProducts)){
                    foreach ($apiProducts as $apiProduct) {
                        $info_product = $apiProduct['product'];
                        // verifico que el producto haya sido seleccionado para mostrar
                        // en wordpress
                        if($info_product['visible_ecommerce']){
                            // verifico que el precio sea positivo
                            if($info_product['price_libre_igv'] > 0){
                                if(!isset($products[$info_product['id']])){
                                    $products[$info_product['id']] = [
                                        'api_product_id' => $info_product['id'],
                                        'precio'         => $info_product['price_libre_igv'],
                                        'precio_inc_igv' => $info_product['price_inc_igv'],
                                        'descripcion'    => $info_product['description'],
                                        'nombre'         => $info_product['name'],
                                        'image'          => $info_product['image'],
                                        'categories'     => ','. $info_product['category_id'] .',',
                                        'SKU_code'       => $info_product['code'],
                                    ];
                                }else{
                                    $cat = $products[$info_product['id']]['categories'];
                                    $products[$info_product['id']]['categories'] =  $cat . $info_product['idCategoria'] .',';
                                }
                            }
                        }
                        
                    }
                }
            }
            db_insert_rows( $products, TABLE_PRODUCTS, true, 'api_product_id');  
        }
        write_log("Finalizo categorias");
         
    }    
    
}
add_action('lars_pos_cron_hook', 'process_lars_pos');

