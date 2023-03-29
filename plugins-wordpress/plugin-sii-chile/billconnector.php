<?php
/**
 * Archivo del plugin 
 * Este archivo es leído por WordPress para generar la información del plugin
 * en el área de administración del complemento. Este archivo también incluye 
 * todas las dependencias utilizadas por el complemento, registra las funciones 
 * de activación y desactivación y define una función que inicia el complemento.
 *
 * @link                https://billconnector.com
 * @since               1.0.0
 * @package             Billconnector
 * @author     			BillConnector <contacto@lars.net.co>
 *
 * @wordpress-plugin
 * Plugin Name:         Billconnector
 * Plugin URI:          https://billconnector.com
 * Version:             1.0.0

 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
define( 'BC_REALPATH_BASENAME_PLUGIN', dirname( plugin_basename( __FILE__ ) ) . '/' );
define( 'BC_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BC_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'BC_FILE', __FILE__);


// define( 'BC_URL', 'http://localhost:8000/');
// define( 'BC_URL_API', 'http://localhost:8000/api/');
// define( 'BC_URL', 'https://app.billconnector.com/');
// define( 'BC_URL_API', 'https://app.billconnector.com/api/');
define( 'BC_URL', 'http://127.0.0.1/signal/');
define( 'BC_URL_API', 'http://127.0.0.1/signal/api/');
// https://app.billconnector.com/api/get-companies-urls

define( 'BC_TABLE', "{$wpdb->prefix}bc_config" );
define( 'BC_TABLE_ORDERS', "{$wpdb->prefix}bc_orders" );
define( 'BC_TABLE_LOGS', "{$wpdb->prefix}bc_logs" );
define( 'BC_TABLE_ANALITYCS', "{$wpdb->prefix}bc_analitycs" );
define( 'BC_TABLE_USERS', "{$wpdb->prefix}users" );
define( 'BC_TABLE_USERS_META', "{$wpdb->prefix}usermeta" );
define( 'BC_TABLE_RESULTS_ANALYTICS', "{$wpdb->prefix}bc_results" );

/**
 * Código que se ejecuta en la activación del plugin
 */
function activate_billconnector() {
    require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-activator.php';
	BC_Activator::activate();
}

/**
 * Código que se ejecuta en la desactivación del plugin
 */
function deactivate_billconnector() {
    require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-deactivator.php';
	BC_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_billconnector' );
register_deactivation_hook( __FILE__, function(){
		'deactivate_billconnector';
		wp_clear_scheduled_hook( 'signal_promo_cron_hook' );
	});


require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-master.php';
require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-read_pdf.php';
require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-helpers.php';
require_once BC_PLUGIN_DIR_PATH . 'includes/bc-send-email.php';


if (!function_exists('is_woocommerce_active')){
	function is_woocommerce_active(){
	    $active_plugins = (array) get_option('active_plugins', array());
	    if(is_multisite()){
		   $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	    }
	    return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins) || class_exists('WooCommerce');
	}
}

if(is_woocommerce_active()) {
    function run_bc_master() {
        $bc_master = new BC_Master;
        $bc_master->run();
    }

    run_bc_master();
}


	function get_all(){
		global $wpdb;
		$query = "SELECT * FROM ". BC_TABLE_ANALITYCS;
		
		$result = $wpdb->get_results( $query );

		foreach ( $result as $key =>  $page )
		{			
			//$result[$key]->big_data=decode($page->big_data);
			$result[$key]->big_data = decode($page->big_data);
		}

		return $result;

	}

	function get_only_new(){
		global $wpdb;
		$query = "SELECT * FROM ". BC_TABLE_ANALITYCS ." WHERE view = 0";
		
		$result = $wpdb->get_results( $query );

		foreach ( $result as $key =>  $page )
		{			
			 $result[$key]->big_data=decode($page->big_data);
			 updateStatus($page->id);
		}

		return $result;
	}

	function get_cant_users(){
		global $wpdb;
		$query = "SELECT ID, user_login FROM ". BC_TABLE_USERS ."";
		
		$result = $wpdb->get_results( $query );		
		return $result;
	}

	function get_data_admin(){
		global $wpdb;
		$query = "SELECT user_nicename FROM ".BC_TABLE_USERS." U JOIN ".BC_TABLE_USERS_META." M on U.ID = M.user_id WHERE M.meta_value LIKE '%administrator%'";
		$result = $wpdb->get_results($query);
		return $result;
	}

	function create_results( $req ){
		global $wpdb;
        

		$name = $response['name']  = $req['name'];
		$response['pdf_report']    = $req['report'];
		$response['jpg_graphic']   = $req['pdf'];
		$response['type']          = $req['type'];
		$response['created']       = $req['created_at'];
		$response['url_img']       = $req['url_img'];
		$response['bill_dice']     = $req['recomendaciones'];
		$response['cant_usuarios'] = $req['cant_usuarios'];
		$response['num_ranking']   = $req['num_ranking'];
		$response['nuevos']        = $req['nuevos'];
		$response['recurrentes']   = $req['recurrente'];
		$response['legend_recom']  = $req['legend'];


		//consulta si ya existe registro en la Bd
		$query = "SELECT * FROM {$wpdb->prefix}bc_results WHERE producto='{$name}'";
		$row = $wpdb->get_row( $query );

		//llamamos a la clase para leer el pdf y almacenarlo.
		$convert_files = new BC_ConvertFIles;
		$loc_jpg = $convert_files->bc_convert_jpg_b64($response['jpg_graphic'], $response['name']);
		$loc_pdf = $convert_files->bc_convert_pdf_b64($response['pdf_report'], $response['name']);

		
		$response['results'] = $wpdb->insert(BC_TABLE_RESULTS_ANALYTICS, 
			array(
				'id' 				   => $response['idDB'],
				'producto'             => $response['name'],
				'promedio'			   => $req['promedio'],
				'image_producto'       => $loc_jpg,
				'pdf_producto'         => $loc_pdf,
				'bill_dice'            => $response['bill_dice'],
				'cant_usuarios'        => $response['cant_usuarios'],
				'num_ranking'          => $response['num_ranking'],
				'nuevos'               => $response['nuevos'],
				'recurrentes'          => $response['recurrentes'],
				'type_recomendation'   => $response['type'],
				'legend_recomendation' => $response['legend_recom'],
				'url_image'			   => $response['url_img'],
				'created_at'           => $response['created'],
			)
		);

		$res = new WP_REST_Response($response);

		if ($response['results']){
			$res->set_status(200);
		}else{
			$res->set_status(400);
		}

		return ['req' => $res];
	}

	function f_clear_results(){
		global $wpdb; 
		$delete = $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}bc_results");
		return $delete;
	}

	function updateStatus($id)
	{
		global $wpdb;		
		$wpdb->update( BC_TABLE_ANALITYCS, array( 'view' => 1), array('id'=>$id));
	}

	

	function decode($encoded) {
		$decoded = "";
		for( $i = 0; $i < strlen($encoded); $i++ ) {
			$b = ord($encoded[$i]);
			$a = $b ^ 30;  // <-- must be same number used to encode the character
			$decoded .= chr($a);
		}
		return $decoded;
	}

	function processs_send_email(){
    
		global $wpdb;
		// se busca la data del admin
		// LIMIT 1 -> en caso de que haya más de un administrador en el sitio.
		$query = "SELECT user_nicename, user_email FROM ".BC_TABLE_USERS." U JOIN ".BC_TABLE_USERS_META." M on U.ID = M.user_id WHERE M.meta_value LIKE '%administrator%' LIMIT 1";
		$result = $wpdb->get_results($query);
		if($result){
			$BC_email = new BC_Send_Email;
			write_log("Empieza proceso envió email.");
			$name_admin  = $result[0]->user_nicename;
			$email_admin = $result[0]->user_email;
			
			$BC_email->principal_mail($name_admin, $email_admin);


		}else{
			write_log("No hay datos del admin. Error!");
		}
	}

	add_action('rest_api_init', function(){
		register_rest_route('bc/v1/', 'getAll', [
				'methods'   =>'GET',
				'callback'  =>'get_all',
		]);
	});

	add_action('rest_api_init', function(){
		register_rest_route('bc/v1/', 'GetOnlyNew', [
				'methods'   =>'GET',
				'callback'  =>'get_only_new',
		]);
	});

	add_action('rest_api_init', function(){
		register_rest_route('bc/v1/', 'get_users', [
				'methods'   =>'GET',
				'callback'  =>'get_cant_users',
		]);
	});

	add_action('rest_api_init', function(){
		register_rest_route('bc/v1/', 'get_admin', [
				'methods'   =>'GET',
				'callback'  =>'get_data_admin',
		]);
	});

	add_action('rest_api_init', function(){
		register_rest_route('bc/v1/', 'clear_results', [
				'methods'   =>'GET',
				'callback'  =>'f_clear_results',
		]);
	});


	add_action('rest_api_init', function(){
		register_rest_route('bc/v1/', 'save_results', [
				'methods'   =>'POST',
				'callback'  =>'create_results',
		]);
	});
	
	add_action('bill_connector_cron_hook_send_email', 'processs_send_email');

	