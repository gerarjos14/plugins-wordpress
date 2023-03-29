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
 * Description:         Emite facturas y Boletas AL TIRO con Woocommerce
 * Version:             2.0.0

 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;

define( 'BILL_REALPATH_BASENAME_PLUGIN', dirname( plugin_basename( __FILE__ ) ) . '/' );
define( 'BILL_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BILL_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'BILL_FILE', __FILE__);




/** -------------------- TABLAS --------------------------- */

/** GENERAL */
define('BC_TABLE_CONFIG_GENERAL', "{$wpdb->prefix}bc_config");
define('BC_TABLE_SERVICES', "{$wpdb->prefix}bc_services");
define('BC_TABLE_FAQ', "{$wpdb->prefix}bc_faq");


/**--------------- ESPACIO PARA DEFINIR URL APIS Y ENDPOINTS -----------------*/
define( 'BILL_URL', 'http://billconnector.test/');
define( 'BILL_URL_API', 'http://billconnector.test/api/');

// define( 'BILL_URL', 'https://app.billconnector.com/');
// define( 'BILL_URL_API', 'https://app.billconnector.com/api/');

// define( 'BILL_URL', 'http://127.0.0.1/signal/');
// define( 'BILL_URL_API', 'http://127.0.0.1/signal/api/');

/**--------------- ESPACIO PARA SLUGS DE SERVICIOS -----------------*/

define('ALEGRA_SLUG', 'billconnector-alegra/billconnector-alegra.php');
define('SIIGO_SLUG', 'billconnector-siigo/siigo-connector.php');
define('SUNAT_SLUG', 'billconnector-sunat/billconnector-sunat.php');
define('SII_SLUG', 'billconnector-sii/billconnector.php');

/**--------------- ESPACIO PARA SLUGS DE SERVICIOS -----------------*/


require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-master.php';



/**
 * Código que se ejecuta en la activación del plugin
 */
function activate_billconnector() {
    require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-activator.php';
	BC_Activator::activate();
}

/**
 * Código que se ejecuta en la desactivación del plugin
 */
function deactivate_billconnector() {
    require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-deactivator.php';
	BC_Deactivator::deactivate();
}

/**
* Código del cron encargado del censo de servicios pagos por parte del cliente
*/
function process_review_services(){
    require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-services.php';
	$services = new BC_Services;
	$services->reviewServicesPaids();

}

function run_bc_master() {
	$bc_master = new BC_Master;
	$bc_master->run();
}

run_bc_master();



register_activation_hook( __FILE__, 'activate_billconnector' );
register_deactivation_hook( __FILE__, function(){
		'deactivate_billconnector';
		wp_clear_scheduled_hook( 'signal_promo_cron_hook' );
	});

// REGISTRO CRON PARTE 2
// nombre cron, funcion que contiene el cron
add_action('bill_connector_cron_hook_paid_services_review', 'process_review_services');

