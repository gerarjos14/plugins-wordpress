<?php

require_once plugin_dir_path(__FILE__) . '/helper.php';

/**
 * FUNCIONES DE CONSULTA A LA BASE DE DATOS
 */
function dbGetProducts($wpdb){
    $query = "SELECT * FROM {$wpdb->prefix}".TABLE_PRODUCTS.";";
    return $wpdb->get_results( $query );
}

function dbGetCategories($wpdb){
    $query = "SELECT * FROM {$wpdb->prefix}".TABLE_CATEGORIES.";";
    return $wpdb->get_results( $query );
}
function dbStoreCategories($wpdb){
    $query = "SELECT * FROM {$wpdb->prefix}".TABLE_CATEGORIES.";";
    return $wpdb->get_results( $query );
}

function dbGetCategory($wpdb, $id){
    $query = "SELECT * FROM {$wpdb->prefix}".TABLE_CATEGORIES." WHERE api_category_id={$id} LIMIT 1;";
    return $wpdb->get_row( $query );
}

/**
 * FUNCIONES PARA CREAR DATOS EN WOOCOMMERCE
 */
function wcStoreCategories($woocommerce, $data){
    
    return $woocommerce->post('products/categories/batch', $data);
}

function wcCreateAttribute($woocommerce){
    $data = [
        'name' => 'Color',
        'slug' => 'lars_pos_color',
        'type' => 'select',
        'order_by' => 'menu_order',
        'has_archives' => true
    ];
    return $woocommerce->post('products/attributes', $data);
}
/**
 * FUNCTIONES DE CONSULTA A LA API EXTERNA
 */
function apiGetCategories(){
    $urlAPI = API_URL . 'api/api_categories/list';
    $response = wp_remote_request($urlAPI, ['method' => 'GET']);
    $result = json_decode(wp_remote_retrieve_body($response), true);
    return $result;
}

function apiGetProductsByCategory($id){
    $urlAPI = API_URL . "api/api_categories/".WAREHOUSE."/categorie/{$id}/products";
    $response = wp_remote_request($urlAPI, ['method' => 'GET']);
    $result = json_decode(wp_remote_retrieve_body($response), true);
    return $result;
}

/**
 * FUNCION PARA ARMAR ARRAY
 */

function dbArrayCreateCategories($array){
    $response = array();
    foreach ($array as $value) {
        for ($i=0; $i < count($value[0]); $i++) { 

            $response[] = [ 
                'w_category_id'   => $value[0][$i]->id,
                'api_category_id' => $value[1][$i]['api_category_id'],
            ];
        }        
    }
    return $response;
}


function arrayEditCategories($categories, $wpdb){
    $response = array();
    $array_categories = array_chunk($categories, 100);
   
    for ($i=0; $i < count($array_categories); $i++) { 
        $create = array();
        $update = array();
        $acc = array();
        
        foreach ($array_categories[$i] as $category) {
            $result = dbGetCategory($wpdb, $category['id']);
            if(!$result){
                $create[] = [ 
                    'name' => $category['name'],
                    // 'image' => [
                    //     'src' => ''
                    // ],
                ];   
            }
        }
        if (count($create)) $acc['create'] = $create;
        if (count($update)) $acc['update'] = $update;        
        if (count($acc)) $response[] = $acc;
    }
    return $response;
}


function arrayCreateCategories($categories){
    $response = array();
    $acc = array();
    $array_categories = array_chunk($categories, 100);
    for ($i=0; $i < count($array_categories); $i++) {         
        foreach ($array_categories[$i] as $category) {
            $acc[] = [
                'name' => $category['name'],
                // 'image' => [
                //     'src' => ''
                // ],
                'api_category_id' => $category['id'],
            ];
        }
        $response[]['create'] = $acc;
    }
   
    return $response;
}
/** --- AGREGAR CAMPOS A CHECKOUT --- */
/**
 * Actions para agregar select en donde el usuario comprador 
 * podrá decidir que tipo de facturación desea para su compra, y por lo 
 * cual el sistema le envíará por mail el archivo pdf correspondiente.
 * 
 * Factura -> 13
 * Boleta -> 31
 * Nada -> blank => error
 */

 // ACTIONS PARA SELECT CON TIPO DE FACTURACIÓN
add_action( 'woocommerce_before_checkout_billing_form', 'lars_pos_field_checkout' );
add_action( 'woocommerce_before_checkout_billing_form', 'lars_pos_add_client_ruc_field' );
add_action( 'woocommerce_before_checkout_billing_form', 'lars_pos_add_client_code_field' );
add_action( 'woocommerce_checkout_process','lars_pos_validate_select_facturas' );
add_action( 'woocommerce_checkout_update_order_meta','lars_pos_save_tipo_factura' );
add_action( 'woocommerce_admin_order_data_after_billing_address','lars_pos_show_custom_tipo_factura' );
add_action( 'woocommerce_thankyou', 'lars_pos_show_tipo_factura_thankyou', 15 );
add_action( 'woocommerce_view_order', 'lars_pos_show_tipo_factura_thankyou', 15 );

/**
 * Función lars_pos_field_checkout
 * @author
 * Acá se le indica a woocommerce que agrege el select con los tipos de facturas.
 */
function lars_pos_field_checkout($checkout){
         woocommerce_form_field( 'tipo_factura', array( 
            'type'     => 'select',
            'class'    => array('state_select'), 
            'required' => true, 
            'label'    => _x('Elige el tipo de facturación', 'placeholder', 'woocommerce'), 
            'options'  => array(
                'blank' => __('Elige el tipo de facturación'),
                '13'    => __('Factura'),
                '31'    => __('Boleta electrónica')),
            'priority' => 40,
        ),
        $checkout->get_value( 'tipo_factura' )); 
}

/**
 * Función para agregar campo RUC del cliente
 * @author 
 */
function lars_pos_add_client_ruc_field($checkout){
    woocommerce_form_field('ruc_cliente', array(
            'type'     => 'number',
            'required' => true,
            'class'    => array('state_select'),
            'label'    => 'R.U.C',
            'priority' => 15
        ), 
        $checkout->get_value('ruc_cliente')
    );
}

/**
 * Función para agregar campo code del cliente
 * @author
 */
function lars_pos_add_client_code_field($checkout){
    woocommerce_form_field('code_cliente', array(
        'type'     => 'hidden',
        'priority' => 20,
        'value'    => 122
    ), 
    $checkout->get_value('code_cliente')
);
}


/**
 * Función lars_pos_validate_select_facturas
 * @author
 * 
 * Se verifica si el usuario ingresó alguna opción. En caso de que no, se informa el error, no se lo
 * deja finalizar la compra
 */
function lars_pos_validate_select_facturas(){
    // valido el tipo de facturación
    if($_POST['tipo_factura'] == 'blank'){
        wc_add_notice( 'Por favor, selecciona un método de facturación.', 'error' );
    }
    
    // verifico si el tipo de factura es Factura
    // en caso de que sí, se procede a validar el RUC
    if($_POST['tipo_factura'] == '13'){
        // Validación ingreso RUC
        $validateRUC = validarInputRUC($_POST['ruc_cliente']);

        if($validateRUC == -1){
            wc_add_notice('Por favor, verifica que el RUC comienze con 10, 15, 16, 17 o 20', 'error');
        }
        if($validateRUC == -2){
            wc_add_notice('Por favor, ingresa un RUC de 11 dígitos', 'error');
        }

        // Fin validación

        if(empty($_POST['ruc_cliente'])){

            wc_add_notice('Por favor, ingresa RUC','error');
        }
    }else{
        // si es boleta electrónica, verifico si el ruc fue ingresado
        if(empty($_POST['ruc_cliente'])){
            // no fue ingresado, pero no es obligatorio, ingreso -1 indica que en TPV debe ser null
            $_POST['ruc_cliente'] = -1;
        }else{
            // se ingresó, hago validaciones
            $validateRUC = validarInputRUC($_POST['ruc_cliente']);

            if($validateRUC == -1){
                wc_add_notice('Por favor, verifica que el RUC comienze con 10, 15, 16, 17 o 20', 'error');
            }
            if($validateRUC == -2){
                wc_add_notice('Por favor, ingresa un RUC de 11 dígitos', 'error');
            }
        }
    }   
}

/**
 * Almaceno la información de la factura
 */
function lars_pos_save_tipo_factura($order_id){
    if( !empty( $_POST['tipo_factura'] ) ){
        update_post_meta( 
            $order_id, 
            'tipo_factura', 
            sanitize_text_field( $_POST['tipo_factura'] ) 
        );
    }

    if( !empty($_POST['ruc_cliente']) ){

        update_post_meta($order_id, 'ruc_cliente', sanitize_text_field($_POST['ruc_cliente']));
    }

    if(!empty($_POST['code_cliente'])){
        update_post_meta($order_id, 'code_cliente', sanitize_text_field($_POST['code_cliente']));
    }
}

/***
 * Se agrega la información de la factura seleccionada en la vista 
 * admin de los pedidos de woocommerce
 * (woocommerce-> pedidos-> ver detalles)
 */
function lars_pos_show_custom_tipo_factura($order){

    if(get_post_meta( $order->id, 'tipo_factura', true )==13){
        $tipo_facturacion = "Factura";
    }else{
        $tipo_facturacion = "Boleta electrónica";
    }
    echo '<p><strong>'.__('RUC Cliente').':</strong> ' . get_post_meta($order->id, 'ruc_cliente', true) . '</p>';   
    echo '<p><strong>'.__('Método de facturación').':</strong> ' . $tipo_facturacion . '</p>';   
}


/**
 * Muestro el tipo de facturación en la vista de la orden en la 
 * página de gracias y en la pág. de orden en la 
 * página 'Mi cuenta' del usuario
 */

 function lars_pos_show_tipo_factura_thankyou($order_id){

    if(get_post_meta( $order_id, 'tipo_factura', true )==13){
        $tipo_facturacion = "Factura";
    }else{
        $tipo_facturacion = "Boleta electrónica";
    }
    echo '<p><strong>'.__('RUC Cliente').':</strong> ' . get_post_meta($order_id, 'ruc_cliente', true) . '</p>';   
    echo '<p><strong>'.__('Método de facturación').':</strong> ' . $tipo_facturacion . '</p>';
 }