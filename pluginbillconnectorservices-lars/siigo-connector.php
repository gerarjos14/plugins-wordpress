<?php
/**
 * Archivo del plugin 
 * Este archivo es leído por WordPress para generar la información del plugin
 * en el área de administración del complemento. Este archivo también incluye 
 * todas las dependencias utilizadas por el complemento, registra las funciones 
 * de activación y desactivación y define una función que inicia el complemento.
 *
 * @link                http://misitioweb.com
 * @since               1.0.0
 * @package             Siigo_Connector
 *
 * @wordpress-plugin
 * Plugin Name:         Siigo_Connector
 * Plugin URI:          http://miprimerplugin.com
 * Description:         Descripción corta de nuestro plugin
 * Version:             1.0.0
 * License:             GPL2
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         siigo-connector-textdomain
 * Domain Path:         /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
define( 'SC_REALPATH_BASENAME_PLUGIN', dirname( plugin_basename( __FILE__ ) ) . '/' );
define( 'SC_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'SC_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'SC_FILE', __FILE__);
define( 'SC_URL_API', "https://api.siigo.com/" );
define( 'SC_TABLE', "{$wpdb->prefix}sc_keys" );
define( 'SC_TABLE_PRODUCTS', "{$wpdb->prefix}sc_products" );
define( 'SC_TABLE_CUSTOMERS', "{$wpdb->prefix}sc_customers" );

/**
 * Código que se ejecuta en la activación del plugin
 */
function activate_siigo_connector() {
    require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-activator.php';
	SC_Activator::activate();
}
/**
 * Código que se ejecuta en la desactivación del plugin
 */
function deactivate_siigo_connector() {
    require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-deactivator.php';
	SC_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_siigo_connector' );
register_deactivation_hook( __FILE__, 'deactivate_siigo_connector' );

require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-master.php';

function run_sc_master() {
    $sc_master = new SC_Master;
    $sc_master->run();
}

run_sc_master();