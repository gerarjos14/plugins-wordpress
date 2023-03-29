<?php

/**
 * Define la funcionalidad de internacionalización
 *
 * Carga y define los archivos de internacionalización de este plugin para que esté listo para su traducción.
 *
 * @link       http://misitioweb.com
 * @since      1.0.0
 *
 * @package    Siigo_Connector
 * @subpackage Siigo_Connector/includes
 */

/**
 * Ésta clase define todo lo necesario durante la activación del plugin
 *
 * @since      1.0.0
 * @package    Siigo_Connector
 * @subpackage Siigo_Connector/includes
 */
class SC_i18n {
    
    /**
	 * Carga el dominio de texto (textdomain) del plugin para la traducción.
	 *
     * @since    1.0.0
     * @access public static
	 */    
    public function load_plugin_textdomain() {
        
        load_plugin_textdomain(
            'sigoo-connector-textdomain',
            false,
            SC_PLUGIN_DIR_PATH . 'languages'
        );
        
    }
    
}