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
	 * check_token
	 * Se chequea si existe el token o no en el sistema
	 * @author Matias
	 */
	public function check_token($token){
		$endpoint_token = BILL_URL_API . 'plugin/check-token/'.$token.'';
		$this->write_log($endpoint_token);
		$response       = wp_remote_request($endpoint_token, ['method' => 'GET']);
		$result         = json_decode(wp_remote_retrieve_body($response), true);
		//$this->write_log($response['data']);
		return $result;
	}


    /**
	 * Helpers de interaccion con la Base de datos
	 */
	public function get_config_db(){
		$query = "SELECT * FROM " . BC_TABLE_CONFIG_GENERAL. " ORDER BY id ASC LIMIT 1";
		$row   = $this->db->get_row($query);
		return $row;
	}

	public function get_faq(){
		$query = "SELECT * FROM " . BC_TABLE_FAQ . " ORDER BY id ASC ";
		$row   = $this->db->get_results($query);
		return $row;
	}

	public function set_config_db($token, $from_api){
		$this->db->insert(BC_TABLE_CONFIG_GENERAL, [
			'token'   => $token,
			'country' => $from_api['country'],
			'type'    => $from_api['rol'],
			'user_id' => $from_api['user_id']
		]);
		return true;
	}

	public function update_config_db($token, $id, $from_api){		
		$this->db->update(BC_TABLE_CONFIG_GENERAL, [
			"token" => $token,
		], [ 'id' => $id ]);
		return true;
	}

	/**
	 * Función checkServices
	 * @author Matias
	 * 
	 */
	function checkServices($user_id){
		$endpoint_token = BILL_URL_API . 'plugin/check-services/'.$user_id.'';
		//$this->write_log($endpoint_token);
		$response       = wp_remote_request($endpoint_token, ['method' => 'GET']);
		$result         = json_decode(wp_remote_retrieve_body($response), true);
		//$this->write_log($result);
		return $result;
	}


	/**
	 * Función para consultar servicios pagos
	 * @author Matias
	 */
	function getServices(){
		$query = "SELECT * FROM " . BC_TABLE_SERVICES ." ORDER BY id ASC;";
		$results = $this->db->get_results( $query );
		return $results;
	}

	
}