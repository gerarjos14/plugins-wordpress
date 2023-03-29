<?php

function write_log ( $log )  {
    if ( true === WP_DEBUG ) {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
}

function wab_action_woocommerce_order_status_processing($order_id) { 
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->prefix}wab_keys ORDER BY id ASC LIMIT 1;";
    $row = $wpdb->get_row( $query );

    if(!$row->order_status){
        if(isset($row)){
            $token = $row->token;     
            if( isset($token) && !empty($token) ){
                $response = wp_remote_post(BC_URL_API . 'create-order', array(
                        'body'    => [
                            'order_id' => $order_id,
                            'token' => $row->token,
                        ],
                    )
                );

                write_log($response);

                
            }      
        }
    }
}; 

add_action( 'woocommerce_order_status_processing', 'wab_action_woocommerce_order_status_processing'); 

function wab_action_woocommerce_payment_complete($order_id){
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->prefix}wab_keys ORDER BY id ASC LIMIT 1;";
    $row = $wpdb->get_row( $query );
    if($row->order_status){        
        if(isset($row)){
            $token = $row->token;     
            if( isset($token) && !empty($token) ){
                $response = wp_remote_post(BC_URL_API . 'create-order', array(
                        'body'    => [
                            'order_id' => $order_id,
                            'token' => $row->token,
                        ],
                    )
                );
            }      
        }
    }
    
}

add_action( 'woocommerce_order_status_completed', 'wab_action_woocommerce_payment_complete');