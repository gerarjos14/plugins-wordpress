<?php

class BC_Helpers {

	/**
	 * Objeto wpdb
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $db @global $wpdb
	 */
    private $db;
	private $normalize;
	
    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
		$this->normalize = new BC_Normalize;
    }



		

    function write_log ( $log )  {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
	/**
	 * Conexion con la api de Billconnector
	 */
	public function create_invoice($body)
	{
		$url = BC_URL_API . 'create-dte';
        $body = wp_json_encode( $body );
		$options = [     
			'method'	  =>'POST',
			'timeout'     => 120,	
            'body'        => $body,
            'headers'     => [
                'Content-Type'  => 'application/json',
            ],
        ];
		
		$response = wp_remote_post($url, $options);
		
		$body_response = json_decode(wp_remote_retrieve_body($response), true);	
		$code = wp_remote_retrieve_response_code($response);
		if($code >= 400) {
			$message = isset($body_response['message']) ? $body_response['message'] : 'Error inesperado';
			throw new Exception($message);
		}
		return $body_response;
	}

	public function cancel_invoice($token, $order_id)
	{
		$url = BC_URL_API . 'cancel-dte';
        $body = wp_json_encode([
			'token' 	=> $token,
			'order_id'	=> $order_id,
		]);
		$options = [     
			'method'	  =>'POST',
			'timeout'     => 120,	
            'body'        => $body,
            'headers'     => [
                'Content-Type'  => 'application/json',
            ],
        ];

		$response = wp_remote_post($url, $options);
        
		$code = wp_remote_retrieve_response_code($response);		
		$body_response = json_decode(wp_remote_retrieve_body($response), true);	
		if($code >= 400) {
			$message = isset($body_response['message']) ? $body_response['message'] : 'Error inesperado';
			throw new Exception($message);
		}
		return $body_response;
	}


	public function make_body_for_external_api($order_id, $token, $store_name)
	{
		$order 		= wc_get_order( $order_id );

        $rut        = get_post_meta($order->get_id(), 'Rut', true);

		$giro        = $this->normalize->init(get_post_meta($order->get_id(), 'Giro', true));

		$type        = get_post_meta($order->get_id(), 'Type', true);
        
		$city       = $this->normalize->init($order->get_billing_city()); // Sin utilizar todavia

        $state_code = $order->get_billing_state();

		
        $state      = $this->normalize->init($this->get_State($state_code));

        $email      = $order->get_billing_email();
        $address    = $this->normalize->init($order->get_billing_address_1());

        $lastName   = $order->get_billing_last_name();
        $firstName  = $this->normalize->init($order->get_billing_first_name());
        $name       = $firstName . ' ' . $lastName; 

        $items = array();		
		
		

		//return $order;
        foreach ( $order->get_items() as $item_id => $item ) {	

			$tax_amount = $item->get_total_tax();		
			
            $product_name = $this->normalize->init($item->get_name());
            $quantity = $item->get_quantity();
            $subtotal = $item->get_subtotal();
			
            $price = $subtotal / $quantity;   
            $item = [
                'description' => $product_name,
                'quantity' => $quantity,
                'unit_price' => round($price, 2) ,
            ];
			if($tax_amount==0)
			{
				$item['exempt']=1;
			}	
			$items[]=$item;
        }

		/**
		 * SI LA ORDEN TIENE ENVIO, LO AGREGAMOS COMO UN ITEM
		 */
		if($order->get_shipping_total()>0)
		{
			$tax_shipping_amount = $order->get_shipping_tax();	
			$shipping_price = $order->get_shipping_total();			
			$item = [
                'description' => 'shipping',
                'quantity'    => 1,
                'unit_price'  =>round($shipping_price, 2),
            ];
			if($tax_shipping_amount==0)
			{
				$item['exempt']=1;
			}	
			$items[]=$item;
		}

		$discount 		= $order->get_discount_total();
		$discount_perc	= false;

        $body = [
						'token'			=> $token,	
						'type'			=> $type,
            'name'      	=> $name,
            'address'   	=> $address,
						'city'			=> $city,
            'state'     	=> $state,
            'email'     	=> $email,			
            'items'     	=> $items,
						'order_id'  	=> $store_name.'_'.$order_id,
        ];

		if($giro!='empty')
		{
			$body['classification']=$giro;
		}	
		if($rut!='66666666-6')
		{
			$body['rut']=$rut;
		}	
		if($discount!=0)
		{
			$body['discount']=$discount;
			$body['discount_perc']=0;
		}			
	

        return $body;
	}

	public function get_state($state_code)
	{
		$states = [
			'CL_AP' => 'Región de Arica y Parinacota',
			'CL_TA' => 'Región de Tarapacá',
			'CL_AN' => 'Región de Antofagasta',
			'CL_AT' => 'Región de Atacama',
			'CL_CO' => 'Región de Coquimbo',
			'CL_VA' => 'Región de Valparaíso',
			'CL_RM' => 'Región Metropolitana de Santiago',
			'CL_OH' => 'Región de O\'Higgins',
			'CL_ML' => 'Región del Maule',
			'CL_NB' => 'Región de Ñuble',
			'CL_BI' => 'Región del Biobío',
			'CL_AR' => 'Región de La Araucanía',
			'CL_LR' => 'Región de Los Ríos',
			'CL_LL' => 'Región de Los Lagos',
			'CL_AI' => 'Región de Aysén del General Carlos Ibáñez del Campo',
			'CL_MA' => 'Región de Magallanes y de la Antártica Chilena'
		];
		return empty($states[$state_code]) ? $state_code : $states[$state_code];
	}


    /**
	 * Helpers de interaccion con la Base de datos
	 */
	public function get_config_db()
	{
		$query = "SELECT * FROM " . BC_TABLE . " ORDER BY id ASC LIMIT 1;";
		$row = $this->db->get_row( $query );
		return $row;
	}

	public function set_config_db($token, $order_status)
	{
		$this->db->insert(BC_TABLE, [
			"token" => $token,
			"unique_id" => $this->getRand(5),
			"order_status" => $order_status,
		]);
		return true;
	}

	public function update_config_db($token, $order_status, $id)
	{		
		$this->db->update(BC_TABLE, [
			"token" => $token,
			"order_status" => $order_status,
		], [ 'id' => $id ]);
		return true;
	}

	// ORDERS
	public function get_order($order_id)
	{
		$query = "SELECT * FROM " . BC_TABLE_ORDERS . 
		" WHERE order_id=" . $order_id .
		" AND (type_document='invoice' OR type_document='ballot')" .
		" ORDER BY id ASC LIMIT 1;";
		
		$row = $this->db->get_row( $query );
		return $row;
	}

	public function get_order_cancel($order_id)
	{
		$query = "SELECT * FROM " . BC_TABLE_ORDERS . 
		" WHERE order_id=" . $order_id .
		" AND type_document='credit_note' " .
		" ORDER BY id ASC LIMIT 1;";
		
		$row = $this->db->get_row( $query );
		return $row;
	}


	public function get_orders_by_id_to_cancel($order_id)
	{
		$query = "SELECT * FROM " . BC_TABLE_ORDERS . 
		" WHERE order_id=" . $order_id . " ORDER BY id ASC;";
		$results = $this->db->get_row( $query );
		return $results;
	}

	public function get_orders_by_id($order_id)
	{
		$query = "SELECT * FROM " . BC_TABLE_ORDERS . 
		" WHERE order_id=" . $order_id . " ORDER BY id ASC;";
		$results = $this->db->get_results( $query );
		return $results;
	}

	public function get_orders()
	{
		$query = "SELECT * FROM " . BC_TABLE_ORDERS ." ORDER BY id ASC;";
		$results = $this->db->get_results( $query );
		return $results;
	}

	public function set_orders($order_id, $unique_id, $type, $link = '')
	{
		$this->db->insert(BC_TABLE_ORDERS, [
			"order_id" 		=> $order_id,
			"unique_id" 	=> $unique_id,
			"type_document" => $type,
			"link"			=> $link
		]);
		return true;
	}
	
	// LOGS
	public function get_logs()
	{
		$query = "SELECT * FROM " . BC_TABLE_LOGS ." ORDER BY id ASC;";
		$results = $this->db->get_results( $query );
		return $results;
	}

	public function set_log($order_id, $status, $message)
	{
		$this->db->insert(BC_TABLE_LOGS, [
			"order_id" 	 => $order_id,
			"status" 	 => $status,
			"message"	 => $message,
			"created_at" => current_time('Y-m-d H:i:s'),
		]);
		return true;
	}

	function getRand($n) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$randomString = '';
	  
		for ($i = 0; $i < $n; $i++) {
			$index = rand(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}
	  
		return $randomString;
	}
}