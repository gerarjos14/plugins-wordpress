<?php
function wab_ajax_change_keys(){
    check_ajax_referer('data_security', 'nonce');
    if( current_user_can('manage_options') ){

        global $wpdb;
        $query = "SELECT * FROM {$wpdb->prefix}wab_keys ORDER BY id ASC LIMIT 1;";
        $row = $wpdb->get_row( $query );
        
        $token = wab_sanitize_text_field($_POST['token'],  $row ? $row->token : null);
        
        $order_status = $_POST['order_status'];
        if($order_status != 0 && $order_status != 1){
            $order_status = 0;
        };

        $data = array(
            'token' => $token,
            'order_status' => $order_status,
        );

        if($row){
            $result = $wpdb->update("{$wpdb->prefix}wab_keys", $data, ['id' => $row->id]);
        }else{
            $result = $wpdb->insert("{$wpdb->prefix}wab_keys", $data);
        }

        $json = json_encode([
            'result'    => $result,
        ]); 

        echo $json;
        wp_die();
    }
    wp_die();
}
function wab_sanitize_text_field($field, $previous){
    if(isset($field)){
        return sanitize_text_field($field);
    }
    return $previous;
}
add_action( 'wp_ajax_wab_change_keys', 'wab_ajax_change_keys' );
