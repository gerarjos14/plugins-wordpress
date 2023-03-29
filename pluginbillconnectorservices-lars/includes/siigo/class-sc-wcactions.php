<?php

class SC_Wcactions {
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

    public function action_woocommerce_order_status_processing($order_id)
    {
        try{
            $row = $this->helpers->get_keys_db();
            if(isset($row) && isset($row->access_token)){
                $order = wc_get_order($order_id);
                
                $customer = $this->helpers->make_array_new_customer_for_siigo($order);
                $response = $this->wpremote->storeCustomer($row->access_token, $customer);

                $this->helpers->write_log($response);
            }
        } catch (Exception $ex) {
            $this->helpers->write_log($ex->getMessage());
        } 
		
    }


    public function action_woocommerce_order_status_complete($order_id)
    {
        try {
            $row = $this->helpers->get_keys_db();
            if (isset($row) && isset($row->access_token)) {                
                $order = wc_get_order($order_id);
                
                // preparación de datos de la factura
                // obtener id del seller 
                $seller = $this->wpremote->getSeller($row->access_token);

                $data_factura = $this->helpers->make_body_factura($order, $seller);
                $this->helpers->write_log($data_factura);
                $response = $this->wpremote->storeFactura($row->access_token, $data_factura);    
                $this->helpers->write_log($response);
            }
        } catch (Exception $ex) {
            $this->helpers->write_log($ex->getMessage());
        }
    }
}