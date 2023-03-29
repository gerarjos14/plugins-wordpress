<?php

class BC_Wc_Actions{

    const INVOICE     = 'invoice';
    const BALLOT      = 'ballot';
    const CREDIT_NOTE = 'credit_note';
    const DEBIT_NOTE  = 'debit_note';

    /**
	 * Objeto BC_Helpers
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      BC_Helpers
	 */
    private $helpers;

    public function __construct()
    {
        $this->helpers = new BC_Helpers;
    }

    public function action_woocommerce_order_status_processing($order_id)
    {
        try{
            $row = $this->helpers->get_config_db();
          
            if( isset($row) && isset($row->token) && !$row->order_status){

                $order = $this->helpers->get_order($order_id);
                if(empty($order)){
                    $body = $this->helpers->make_body_for_external_api($order_id, $row->token, $row->unique_id);
                    $response = $this->helpers->create_invoice($body); 
                    $this->helpers->set_orders($order_id, $response['type'], $response['link']); 
                }

            }
        } catch (Exception $ex) {
            $this->helpers->write_log($ex->getMessage());
            $this->helpers->set_log($order_id, 'error', $ex->getMessage());
        } 
    }
    
    public function action_woocommerce_order_status_complete($order_id)
    {
        try{
            $row = $this->helpers->get_config_db();
            if( isset($row) && isset($row->token) && $row->order_status){

                $order = $this->helpers->get_order($order_id);
                if(empty($order)){
                    $body = $this->helpers->make_body_for_external_api($order_id, $row->token,$row->unique_id);                 
                    $response = $this->helpers->create_invoice($body);                     
                    $this->helpers->set_orders($order_id, $response['id'], $response['type'], $response['link']); 
                }

            }
        } catch (Exception $ex) {
            $this->helpers->write_log($ex->getMessage());
            $this->helpers->set_log($order_id, 'error', $ex->getMessage());
        } 
    }

    /**
     * Funcion del gancho cancelar orden
     */
    public function action_woocommerce_order_status_cancelled($order_id)
    {
        try{
            $row = $this->helpers->get_config_db();
            if( isset($row) && isset($row->token) ){
                $order = $this->helpers->get_orders_by_id_to_cancel($order_id);
                $order_cancel = $this->helpers->get_order_cancel($order_id);
                // Solo si la orden genero una factura o boleta le permito cancelar
                if( isset($order) && empty($order_cancel) ){                                      
                    $response = $this->helpers->cancel_invoice($row->token, $order->unique_id);     
                    if(!empty($response['type'])&&!empty($response['type']))      
                    {
                        $this->helpers->set_orders($order_id, $order->unique_id, $response['type'], $response['link']); 
                    }else{
                        $this->helpers->write_log($response);
                    }                             
                }
            }
        } catch (Exception $ex) {
            $this->helpers->write_log($ex->getMessage());
            $this->helpers->set_log($order_id, 'error', $ex->getMessage());

        }
    }
}