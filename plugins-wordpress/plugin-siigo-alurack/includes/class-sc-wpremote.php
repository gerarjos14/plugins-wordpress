<?php

class SC_wpremote {
    public function __construct()
    {
        $this->helpers = new SC_Helpers;
    }

    /**
     * WOOCOMMERCE
     */
    public function storeAndUpdateWcProduct($woocommerce, $products)
    {    
        return $woocommerce->post('products/batch', $products);
    }

    /**
     * SIIGO Api
     */
    public function authentication($username, $access_key)
    {
        $url = SC_URL_API . "auth";
        
        $body = [
            'username'   => $username,
            'access_key' => $access_key,
        ];
        $body = wp_json_encode( $body );

        $options = [
            'body'        => $body,
            'headers'     => [
                'Content-Type' => 'application/json',
            ],
        ];

        $response = wp_remote_post($url, $options);
        $response = json_decode(wp_remote_retrieve_body($response), true);
        if(empty($response)){
		    throw new Exception("Fallo la petición");
        }
        if(isset($response["Errors"])){
		    throw new Exception("Usuario o claves invalidos.");
        }
        return $response;
    }

    public function getProducts($token, $page, $page_size)
    {

        // Query Params
        $params = "?page={$page}&page_size={$page_size}";

        $url = SC_URL_API . "v1/products" . $params;
        $options = [            
            'headers'     => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '. $token
            ],
        ];

        $response = wp_remote_get($url, $options);
        
        $response = json_decode(wp_remote_retrieve_body($response), true);
        if(empty($response)){
		    throw new Exception("Falló la petición: " . $url);
        }
        if(isset($response["Errors"])){
		    throw new Exception("Ha ocurrido un error inesperado.");
        }
        return $response;
    }

    public function storeCustomer($token, $customer)
    {
        $url = SC_URL_API . "v1/customers";

        $body = [
            "person_type"       => "Person",
            "id_type"           => $customer['tipo_identificacion'],
            "identification"    => $customer["identification"],
            "name"              => [ $customer["first_name"], $customer["last_name"] ],
            "phones"             => [ [ "number" => $customer["phone"] ] ],
            "address"           => [
                "address" => $customer["address"],
                "city" => [
                    "country_code"  => "Co",
                    "state_code"    => $customer["state_code"],
                    "city_code"     => $customer["city_code"],
                ]
            ],
            "contacts" => [
                [
                    "first_name" => $customer["first_name"],
                    "last_name"  => $customer["last_name"],
                    "email"      => $customer["email"]
                ]
            ]
        ];
        $body = wp_json_encode( $body );

        $options = [     
            'body'        => $body,
            'headers'     => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '. $token
            ],
        ];

        $response = wp_remote_post($url, $options);
        $response = json_decode(wp_remote_retrieve_body($response), true);
        if(empty($response)){
		    throw new Exception("Fallo la petición: " . $url);
        }

        if(isset($response["Errors"])){
            $this->helpers->write_log($response['Errors']);

		    throw new Exception("Ha ocurrido un error inesperado.");
        }

        return $response;
    }

    public function storeFactura($token, $data_factura){
        $url = SC_URL_API . "v1/invoices";
        $items_order =  $data_factura['items'];
        
        $body = [
            "document" => [
                "id" => 24446
            ],
            "date" => $data_factura['date'],
            "customer" => [
                "identification" => $data_factura['customer'],
                "branch_office" => 0,
            ],
            "seller" => $data_factura['seller'],
            "items" => $items_order,
            "payments" => [
                [
                    "id" => $data_factura['payments_id'],
                    "value" => sprintf('%0.2f', $data_factura['payments_value']), 
                    "due_date" => $data_factura['date']
                ]
            ]
        ];
        
        $body = wp_json_encode($body);

        $options = [     
            'body'        => $body,
            'headers'     => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '. $token
            ],
        ];

        $response = wp_remote_post($url, $options);
        $response = json_decode(wp_remote_retrieve_body($response), true);
        if(empty($response)){
            throw new Exception("Fallo la petición: " . $url);
        }

        if(isset($response["Errors"])){
            $this->helpers->write_log($response['Errors']);

            throw new Exception("Ha ocurrido un error inesperado.");
        }
        
        return $response;
    }


    public function getSeller($token){
        
        global $wpdb;
        $this->db = $wpdb;

        $url = SC_URL_API . "v1/users";
        $options = [            
            'headers'     => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '. $token
            ],
        ];

        $response = wp_remote_get($url, $options);
        $response = json_decode(wp_remote_retrieve_body($response), true);
        if(empty($response)){
		    throw new Exception($response . "Fallo la petición: " . $url);
        }
        if(isset($response["Errors"])){
		    throw new Exception("Ha ocurrido un error inesperado.");
        }
        $a= $response['results']; /// resultados con seller

        //consulta a BD para saber el username del seller registrado
        $query = "SELECT * FROM " . SC_TABLE . "";
		$row = $this->db->get_row( $query );		
        $username_site = $row->username;

        foreach($a as $results){
            if($results['username'] == $username_site){
                $id_seller = $results['id'];
                break;
            }
        }

        return $id_seller;

        
    }
}