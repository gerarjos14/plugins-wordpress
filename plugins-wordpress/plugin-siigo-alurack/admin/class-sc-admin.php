<?php

/**
 * La funcionalidad específica de administración del plugin.
 *
 * @since      1.0.0
 *
 * @package    Siigo_Connector
 * @subpackage Siigo_Connector/admin
 */

/**
 * Define el nombre del plugin, la versión y dos métodos para
 * Encolar la hoja de estilos específica de administración y JavaScript.
 * 
 * @since      1.0.0
 * @package    Siigo_Connector
 * @subpackage Siigo_Connector/admin
 * 
 * @property string $plugin_name
 * @property string $version
 */
class SC_Admin {
    
    /**
	 * El identificador único de éste plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name  El nombre o identificador único de éste plugin
	 */
    private $plugin_name;
    
    /**
	 * Versión actual del plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version  La versión actual del plugin
	 */
    private $version;
    
    /**
	 * Objeto registrador de menús
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $build_menupage  Instancia del objeto SC_Build_Menupage
	 */
    private $build_menupage;
    
    /**
	 * Objeto wpdb
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $db @global $wpdb
	 */
    private $db;
    
    /**
	 * Objeto SC_CRUD_JSON
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $crud_json Instancia del objeto SC_CRUD_JSON
	 */
    private $crud_json;
    
    /**
	 * Objeto SC_Normalize
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $normalize Instancia del objeto SC_Normalize
	 */
    private $normalize;
    
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
	 * @var      object    $wpremote Instancia del objeto SC_wpremote
	 */
    private $wpremote;

    /**
     * @param string $plugin_name nombre o identificador único de éste plugin.
     * @param string $version La versión actual del plugin.
     */
    public function __construct( $plugin_name, $version ) {
        
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->build_menupage = new SC_Build_Menupage();
        $this->normalize = new SC_Normalize;
        $this->helpers = new SC_Helpers;
        $this->wpremote = new SC_wpremote;
        
        global $wpdb;
        $this->db = $wpdb;
        
    }
    
    /**
	 * Registra los archivos de hojas de estilos del área de administración
	 *
	 * @since    1.0.0
     * @access   public
     *
     * @param    string   $hook    Devuelve el texto del slug del menú con el texto toplevel_page
	 */
    public function enqueue_styles( $hook ) {
        
        /**
         * Una instancia de esta clase debe pasar a la función run()
         * definido en SC_Cargador como todos los ganchos se definen
         * en esa clase particular.
         *
         * El SC_Cargador creará la relación
         * entre los ganchos definidos y las funciones definidas en este
         * clase.
		 */
    
        /**
         * Condicional para controlar la carga de los archivos
         * solamente en la página del plugin
         */
        if( $hook != 'toplevel_page_sc' ) {
            return;
        }
        
        /**
         * Bootstrap
         */
		wp_enqueue_style( 'sc_bootstrap_admin_css', SC_PLUGIN_DIR_URL . 'helpers/bootstrap/css/bootstrap.min.css', array(), '4.6.0', 'all' );
        
        /**
         * Sweet Alert
         * http://t4t5.github.io/sweetalert/
         */
		wp_enqueue_style( 'sc_sweet_alert_css', SC_PLUGIN_DIR_URL . 'helpers/sweetalert-master/dist/sweetalert.css', array(), $this->version, 'all' );
        
        /**
         * sc-admin.css
         * Archivo de hojas de estilos principales
         * de la administración
         */
		wp_enqueue_style( $this->plugin_name, SC_PLUGIN_DIR_URL . 'admin/css/sc-admin.css', array(), $this->version, 'all' );
        
    }
    
    /**
	 * Registra los archivos Javascript del área de administración
	 *
	 * @since    1.0.0
     * @access   public
     *
     * @param    string   $hook    Devuelve el texto del slug del menú con el texto toplevel_page
	 */
    public function enqueue_scripts( $hook ) {
        
        /**
         * Una instancia de esta clase debe pasar a la función run()
         * definido en SC_Cargador como todos los ganchos se definen
         * en esa clase particular.
         *
         * El SC_Cargador creará la relación
         * entre los ganchos definidos y las funciones definidas en este
         * clase.
		 */
        
        /**
         * Condicional para controlar la carga de los archivos
         * solamente en la página del plugin
         */
        if( $hook != 'toplevel_page_sc' ) {
            return;
        }
        
        wp_enqueue_media();
        
        /**
         * Framework Materializecss
         * http://materializecss.com/getting-started.html
         * Material Icons Google
         */
		wp_enqueue_script( 'sc_bootstrap_admin_js', SC_PLUGIN_DIR_URL . 'helpers/bootstrap/js/bootstrap.min.js', ['jquery'], '4.6.0', true );
        
        /**
         * Sweet Alert
         * http://t4t5.github.io/sweetalert/
         */
		wp_enqueue_script( 'sc_sweet_alert_js', SC_PLUGIN_DIR_URL . 'helpers/sweetalert-master/dist/sweetalert.min.js', ['jquery'], $this->version, true );
        
        /**
         * sc-admin.js
         * Archivo Javascript principal
         * de la administración
         */
        wp_enqueue_script( 'siigo_connector_global', SC_PLUGIN_DIR_URL . 'admin/js/siigo-connector-global.js', ['jquery'], $this->version, true );
        
        /**
         * sc-admin.js
         * Archivo Javascript principal
         * de la administración
         */
        wp_enqueue_script( $this->plugin_name, SC_PLUGIN_DIR_URL . 'admin/js/sc-admin.js', ['jquery'], $this->version, true );
        
        /**
         * Lozalizando el archivo Javascript
         * principal del área de administración
         * para pasarle el objeto "sc" con los parámetros:
         * 
         * @param sc.url        Url del archivo admin-ajax.php
         * @param sc.seguridad  Nonce de seguridad para el envío seguro de datos
         */
        wp_localize_script(
            $this->plugin_name,
            'sc',
            [
                'url'       => admin_url( 'admin-ajax.php' ),
                'seguridad' => wp_create_nonce( 'sc_seg' )
            ]
        );
        
    }
    
    /**
	 * Registra los menús del plugin en el
     * área de administración
	 *
	 * @since    1.0.0
     * @access   public
	 */
    public function add_menu() {
        
        $this->build_menupage->add_menu_page(
            __( 'Siigo Connector', 'sc-textdomain' ),
            __( 'Siigo Connector', 'sc-textdomain' ),
            'manage_options',
            'sc',
            [ $this, 'controlador_display_menu' ],
            'dashicons-database-import',
            22
        );
        
        $this->build_menupage->run();
        
    }
    
    /**
	 * Controla las visualizaciones del menú
     * en el área de administración
	 *
	 * @since    1.0.0
     * @access   public
	 */
    public function controlador_display_menu() { 
        require_once SC_PLUGIN_DIR_PATH . 'admin/partials/sc-admin-display.php';
    }
    
    /**
	 * Método que controla el envío
     * de datos con POST, desde el lado público
     * hacia el lado del servidor
	 *
	 * @since    1.0.0
     * @access   public
	 */
    public function ajax_keys() {
        
        check_ajax_referer( 'sc_seg', 'nonce' );
        
        if( current_user_can( 'manage_options' ) ) {  

            extract( $_POST, EXTR_OVERWRITE ); 

            $row = $this->helpers->get_keys_db();
            if($row){
                $this->helpers->update_keys_db($website, $username, $access_key, $consumer_key, $consumer_secret, $row->id);
            }else{
                $this->helpers->set_keys_db($website, $username, $access_key, $consumer_key, $consumer_secret);
            }

            $json = json_encode( [
                "result" => true,
            ] );
            echo $json;
            wp_die();
            
        }
        
    }
}
