<?php

/**
 * Se activa en la desactivación del plugin
 *
 * @since      1.0.0
 *
 * @package    Siigo_Connector
 * @subpackage Siigo_Connector/includes
 */

/**
 * Ésta clase define todo lo necesario durante la desactivación del plugin
 *
 * @since      1.0.0
 * @package    Siigo_Connector
 * @subpackage Siigo_Connector/includes
 */

class SC_Deactivator {

	/**
	 * Método estático
	 *
	 * Método que se ejecuta al desactivar el plugin
	 *
	 * @since 1.0.0
     * @access public static
	 */
	public static function deactivate() {

        wp_clear_scheduled_hook( 'sc_get_products' );
    	wp_clear_scheduled_hook( 'sc_get_token' );
    	wp_clear_scheduled_hook( 'sc_woocommerce_product' );

        flush_rewrite_rules();
        
	}

}