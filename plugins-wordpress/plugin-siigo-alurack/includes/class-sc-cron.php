<?php

use Automattic\WooCommerce\Client;

class SC_cron {

    /**
	 * Objeto SC_Helpers
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      SC_Helpers
	 */
    private $helpers;

    /**
	 * Objeto SC_wpremote
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      SC_wpremote
	 */
    private $wpremote;


    public function __construct()
    {
        $this->helpers = new SC_Helpers;
        $this->wpremote = new SC_wpremote;
    }

    /**
     * Intervalos personalizados para el cron
     */
    public function intervals($intervals)
    {
        $intervals["media_hora"] = [
            "interval" => 60*30,
            "display"  => 'Cada media hora'            
        ];
        return $intervals;
    }

    public function initializer()
    {
        if(! wp_next_scheduled('sc_get_products') ) {
            // $start_in = time() + (5);
            $start_in = time() + (60*90);
            wp_schedule_event( $start_in, 'media_hora', 'sc_get_products' ); 
        }   
        if(! wp_next_scheduled('sc_get_token') ) {
            // $start_in = time() + (5);
            $start_in = time() + (60*90);
            wp_schedule_event( $start_in, 'twicedaily', 'sc_get_token' ); 
        }   
        if(! wp_next_scheduled('sc_woocommerce_product') ) {
            // $start_in = time() + (5);
            $start_in = time() + (60*90);
            wp_schedule_event( $start_in, 'twicedaily', 'sc_woocommerce_product' ); 
        }   
    }

    public function get_token_remote()
    {
        try{ 
            $row = $this->helpers->get_keys_db();
            if($row){
                $response = $this->wpremote->authentication(
                    $row->username, 
                    $row->access_key
                );
                $this->helpers->set_access_token($response["access_token"], $row->id);
            }
        } catch (Exception $ex) {
            $this->helpers->write_log($ex->getMessage());
        }       
    }

    public function get_products()
    {
        try{
            $row = $this->helpers->get_keys_db();
            if(isset($row) && isset($row->access_token)){
                $result = [];
                $page = 1;
                do {
                    $response = $this->wpremote->getProducts(
                        $row->access_token,
                        $page,
                        100
                    );
                    $result = array_merge($result, $response["results"]);
                    $page++;
                } while ($page < 11);
                /*
                do {
                    $response = $this->wpremote->getProducts(
                        $row->access_token,
                        $page,
                        5
                    );
                    $result = array_merge($result, $response["results"]);
                    $page++;
                } while (!empty($response["_links"]["next"]));
                */
               
                $productsForDb = $this->helpers->make_array_for_db($result);
                $this->helpers->db_insert_rows(
                    $productsForDb,
                    SC_TABLE_PRODUCTS,
                    true,
                    'siigo_id'
                ); 
                $this->helpers->write_log($productsForDb);

            }
        } catch (Exception $ex) {
            $this->helpers->write_log($ex->getMessage());
        } 
    }
    
    public function store_and_update_woocommerce_product()
    {
        try {
            $this->helpers->write_log("Comenzo store_and_update_woocommerce_product");
            $row = $this->helpers->get_keys_db();
            if(isset($row) && isset($row->website) && isset($row->consumer_key) && isset($row->consumer_secret)) {
                
                $woocommerce = new Client(
                    $row->website,
                    $row->consumer_key,
                    $row->consumer_secret,      
                    [ 'wp_api'=> true, 'version' => 'wc/v3', 'verify_ssl' => false]	
                ); 
                $this->helpers->write_log( $woocommerce);



                $products = $this->helpers->get_products_db();
                
                $arrayProductsForWc = $this->helpers->make_array_for_wc($products);
                $newProducts = array();
                
                foreach($arrayProductsForWc as $productsForWc){
                    
                    $wcProducts = $this->wpremote->storeAndUpdateWcProduct(
                        $woocommerce,
                        $productsForWc
                    );                   

                    if(!empty($wcProducts->create)){
                        array_push($newProducts, [
                            $wcProducts->create, 
                            $productsForWc['create'] 
                        ]);
                    }
                }
                if(count( $newProducts )){
                    $productsForDb = $this->helpers->make_array_new_products_for_db($newProducts);
                    $this->helpers->db_insert_rows(
                        $productsForDb,
                        SC_TABLE_PRODUCTS,
                        true,
                        'siigo_id'
                    );
                }
            }
            
            $this->helpers->write_log("Finalizo store_and_update_woocommerce_product");

        } catch (Exception $ex) {
            $this->helpers->write_log( $ex);

            $this->helpers->write_log( $ex->getMessage());
        }
    }
}