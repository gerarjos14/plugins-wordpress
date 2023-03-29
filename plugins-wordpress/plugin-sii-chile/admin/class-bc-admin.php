<?php

/**
 * La funcionalidad específica de administración del plugin.
 *
 * @link       http://billconnector.com
 * @since      1.0.0
 *
 * @package    Billconnector
 * @subpackage Billconnector/admin
 */

/**
 * Define el nombre del plugin, la versión y dos métodos para
 * Encolar la hoja de estilos específica de administración y JavaScript.
 * 
 * @since      1.0.0
 * @package    Billconnector
 * @subpackage Billconnector/admin
 * @author     BillConnector <contacto@lars.net.co>
 * 
 * @property string $plugin_name
 * @property string $version
 */
class BC_Admin {
    
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
	 * @var      object    $build_menupage  Instancia del objeto BC_Build_Menupage
	 */
    private $build_menupage;
    
    /**
	 * Objeto BC_Helpers
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      BC_Helpers
	 */
    private $helpers;

    /**
     * @param string $plugin_name nombre o identificador único de éste plugin.
     * @param string $version La versión actual del plugin.
     */
    public function __construct( $plugin_name, $version ) {
        
        $this->plugin_name = $plugin_name;
        $this->version = $version;     
        $this->build_menupage = new BC_Build_Menupage();

        $this->helpers = new BC_Helpers;

    }
    
    /**
	 * Registra los archivos de hojas de estilos del área de administración
	 *
	 * @since    1.0.0
     * @access   public
	 * @param    string   $hook    Devuelve el texto del slug del menú con el texto toplevel_page
	 */
    public function enqueue_styles( $hook ) {
        
        /**
         * Una instancia de esta clase debe pasar a la función run()
         * definido en BC_Cargador como todos los ganchos se definen
         * en esa clase particular.
         *
         * El BC_Cargador creará la relación
         * entre los ganchos definidos y las funciones definidas en este
         * clase.
		 */
        
        /**
         * Condicional para controlar la carga de los archivos
         * solamente en la página del plugin
         */
        if( $hook != 'toplevel_page_bc' ) {
            return;
        }

        /**
         * Bootstrap
         */
		wp_enqueue_style( 'bc_bootstrap_admin_css', BC_PLUGIN_DIR_URL . 'helpers/bootstrap/css/bootstrap.min.css', array(), '4.6.0', 'all' );

		wp_enqueue_style( $this->plugin_name, BC_PLUGIN_DIR_URL . 'admin/css/bc-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name, BC_PLUGIN_DIR_URL . 'admin/css/bc-analytics.css', array(), $this->version, 'all' );

    }
    
   /**
	 * Registra los archivos Javascript del área de administración
	 *
	 * @since    1.0.0
     * @access   public
     *
     * @param    string   $hook    Devuelve el texto del slug del menú con el texto toplevel_page
	 */
    public function enqueue_scripts($hook) {
        
        /**
         * Una instancia de esta clase debe pasar a la función run()
         * definido en BC_Cargador como todos los ganchos se definen
         * en esa clase particular.
         *
         * El BC_Cargador creará la relación
         * entre los ganchos definidos y las funciones definidas en este
         * clase.
		 */

        /**
         * Condicional para controlar la carga de los archivos
         * solamente en la página del plugin
         */
        if( $hook != 'toplevel_page_bc' ) {
            return;
        }

        wp_enqueue_media();
        
        /**
         * Bootstrap
         */
		wp_enqueue_script( 'bc_bootstrap_admin_js', BC_PLUGIN_DIR_URL . 'helpers/bootstrap/js/bootstrap.min.js', ['jquery'], '4.6.0', true );
        
         /**
         * bc-admin.js
         * Archivo Javascript principal
         * de la administración
         */
        wp_enqueue_script( 'billconnector_global', BC_PLUGIN_DIR_URL . 'admin/js/billconnector-global.js', ['jquery'], $this->version, true );
        
        /**
         * bc-admin.js
         * Archivo Javascript principal
         * de la administración
         */
        wp_enqueue_script( $this->plugin_name, BC_PLUGIN_DIR_URL . 'admin/js/bc-admin.js', ['jquery'], $this->version, true );
               
        /**
         * Lozalizando el archivo Javascript
         * principal del área de administración
         * para pasarle el objeto "sc" con los parámetros:
         * 
         * @param bc.url        Url del archivo admin-ajax.php
         * @param bc.seguridad  Nonce de seguridad para el envío seguro de datos
         */
        wp_localize_script(
            $this->plugin_name,
            'bc',
            [
                'url'       => admin_url( 'admin-ajax.php' ),
                'seguridad' => wp_create_nonce( 'bc_seg' )
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
            __( 'Billconnector', 'bc-textdomain' ),
            __( 'Billconnector', 'bc-textdomain' ),
            'manage_options',
            'bc',
            [ $this, 'controlador_display_menu' ],
            'dashicons-rss',
            22
        );
        //$parentSlug, $pageTitle, $menuTitle, $capability, $menuSlug, $functionName
        $this->build_menupage->add_submenu_page(
            'bc',
            __( 'Analisis', 'bc-textdomain' ),
            __( 'Analisis', 'bc-textdomain' ),
            'manage_options',
            'bc_options_analisis',
            [ $this, 'controlador_display_submenu' ],

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
        require_once BC_PLUGIN_DIR_PATH . 'admin/partials/bc-admin-display.php';
    }
    
    public function controlador_display_submenu(){
        require_once BC_PLUGIN_DIR_PATH . 'admin/partials/bc-admin-analytics.php';
    }

    /**
	 * Método que controla el envío
     * de datos con POST, desde el lado público
     * hacia el lado del servidor
	 *
	 * @since    1.0.0
     * @access   public
	 */
    public function ajax_config() {        
        check_ajax_referer( 'bc_seg', 'nonce' );        
        if( current_user_can( 'manage_options' ) ) {  

            extract( $_POST, EXTR_OVERWRITE ); 
  
            $row = $this->helpers->get_config_db();
            if($row){
                $this->helpers->update_config_db($token, $order_status, $row->id);
            }else{
                $this->helpers->set_config_db($token, $order_status);
            }

            $json = json_encode( [
                "result" => true,
            ] );
            echo $json;
            wp_die();            
        }        
    }

    public function ajax_search_order() {
        check_ajax_referer( 'bc_seg', 'nonce' );        
        if( current_user_can( 'manage_options' ) ) {  

            extract( $_POST, EXTR_OVERWRITE ); 

            $documents = $this->helpers->get_orders_by_id($nro_order);

            if(empty($documents)){
                $response = '<p>No hay resultados</p>';
            }else{
                $response = '<table class="wp-list-table widefat fixed striped table-view-list posts mt-4">
                <thead>
                <tr>
                <th scope="col">Nro Pedido</th>
                <th scope="col">Documento</th>
                <th scope="col">Comprobante</th>
                <th scope="col">Ir a BillConnector</th>
                </tr>
                </thead>
                <tbody>';
                foreach ($documents as $key => $document) {
                    $response .= '<tr>';
                    $response .= '<td>'. $document->order_id .'</td>';
                    $response .= '<td>'. $this->get_type_document($document->type_document) .'</td>';
                    $response .= '<td><a class="btn btn-primary" target="_blank" href="'.$document->link.'">Ver Comprobante</a></td>';
                    $response .= '<td><a class="btn btn-success" target="_blank" href="'.BC_URL.'order/'.$document->unique_id.'">Ver en BillConnector</a></td>';
                    $response .= '</tr> ';
                }
                $response .= '</tbody></table>';
            }
            $json = json_encode([
                'html'   => $response,
            ]);
            echo $json;
            wp_die();            
        }  
    }

    private function get_type_document($type_document)
    {
        $documents = [
            'invoice' => 'Factura electrónica',
            'ballot' => 'Boleta electrónica',
            'credit_note' => 'Nota de crédito',
            'debit_note' => 'Nota de débito',
        ];
        return $documents[$type_document];
    }
}







