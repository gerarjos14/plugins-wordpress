<?php
/**
 * Plugin Name: Woocommerce Alegra Billing
 * Plugin URI:
 * Description: Plugin para integrar alegra a woocommerce
 * Version: 1.0
 * Author: LARS Software Company
 * Author URI: https://lars.com.co/
 * License: GPL2
 * License URI: https://www.gnu.org/licences/gpl-2.0.html
 * Text Domain: Woocommerce Alegra Billing
 */

if( ! defined('ABSPATH') ){
    die("Oh, there\'s nothing to see here.");
}

define( 'BC_URL', 'http://billconnector.test/');
define( 'BC_URL_API', 'http://billconnector.test/api/');
//define( 'BC_URL', 'https://app.billconnector.com/');
//define( 'BC_URL_API', 'https://app.billconnector.com/api/');

define( 'BC_REALPATH_BASENAME_PLUGIN', dirname( plugin_basename( __FILE__ ) ) . '/' );
define( 'BC_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BC_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'BC_FILE', __FILE__);


/**
 * The code that runs during plugin activation.
 */
register_activation_hook(__FILE__, 'wab_tb_keys_create');
function wab_tb_keys_create(){
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    global $wpdb;

    $table_name = $wpdb->prefix . "wab_keys";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        token varchar(255) NOT NULL,
        order_status BOOLEAN NOT NULL DEFAULT 0,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    dbDelta( $sql );
}

/**
 * The code that runs during plugin deactivation.
 */
register_deactivation_hook(__FILE__, function(){
    //
});

/**
 * Añade el menu
 */
if(is_admin()){
    require_once plugin_dir_path(__FILE__) . '/includes/menu.php';
}

/**
 * Enqueue scripts and styles
 */
add_action('admin_enqueue_scripts', function($hook){
    if($hook === 'toplevel_page_woocommerce-alegra-billing'){
        wp_enqueue_script('bootstrapJs', plugins_url('assets/bootstrap/js/bootstrap.min.js', __FILE__), ['jquery']);
        wp_enqueue_style('bootstrapCSS', plugins_url('assets/bootstrap/css/bootstrap.min.css', __FILE__));
        wp_enqueue_script('alegraBillingJs', plugins_url('assets/js/woocommerce-alegra-billing.js', __FILE__), ['jquery']);

        wp_localize_script(
            'alegraBillingJs',
            'ajaxData',
            [
                'ajax_url'  => admin_url('admin-ajax.php'),
                'nonce'     => wp_create_nonce('data_security')
            ]
        );
    }
});

/**
 * Insert or Update Keys
 */
if(is_admin()){
    require_once plugin_dir_path(__FILE__) . '/includes/handle-db.php';
}

/**
 * Hook(Action) after payment complete
 */
require_once plugin_dir_path(__FILE__) . '/includes/action-payment-complete.php';
