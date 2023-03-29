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
	 * Objeto BC_SERVICES
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      BC_Services
	 */
    private $services;

    /**
	 * Objeto BC_SERVICES
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      BC_Helpers_Services
	 */
    private $helper_service;

    private $des_services;


    /**
     * @param string $plugin_name nombre o identificador único de éste plugin.
     * @param string $version La versión actual del plugin.
     */
    public function __construct( $plugin_name, $version ) {
        
        $this->plugin_name    = $plugin_name;
        $this->version        = $version;     
        $this->build_menupage = new BC_Build_Menupage();
        $this->helpers        = new BC_Helpers;
        $this->services       = new BC_Services;
        $this->helper_service = new BC_Helpers_Services;
        $this->des_services   = new BC_DeactivatorServices;


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
		wp_enqueue_style( 'bc_bootstrap_admin_css', BILL_PLUGIN_DIR_URL . 'helpers/bootstrap/css/bootstrap.min.css', array(), '4.6.0', 'all' );

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
		wp_enqueue_script( 'bc_bootstrap_admin_js', BILL_PLUGIN_DIR_URL . 'helpers/bootstrap/js/bootstrap.min.js', ['jquery'], '4.6.0', true );
        
         /**
         * bc-admin.js
         * Archivo Javascript principal
         * de la administración
         */
        wp_enqueue_script( 'billconnector_global', BILL_PLUGIN_DIR_URL . 'admin/js/billconnector-global.js', ['jquery'], $this->version, true );
        
        /**
         * bc-admin.js
         * Archivo Javascript principal
         * de la administración
         */
        wp_enqueue_script( $this->plugin_name, BILL_PLUGIN_DIR_URL . 'admin/js/bc-admin.js', ['jquery'], $this->version, true );

               
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
            'bc_services',
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
            'billconnector',
            [ $this, 'controlador_display_menu' ],
            'dashicons-rss',
            22
        );
        $kname = 'Alegra';
        $this->build_menupage->add_submenu_page(
            'bc',
            __( $kname, 'bc-textdomain' ),
            __( $kname, 'bc-textdomain' ),
            'manage_options',
            'bc_options_' . $kname,
            [ $this, 'controlador_display_submenu_alegra' ],
        );

        // Busco servicios que estén activo y los muestro según foreach
        global $wpdb;
        $services = "SELECT name FROM " .BC_TABLE_SERVICES. " WHERE active = '1' ";
        $results  = $wpdb->get_results( $services );

        foreach($results as $key){
            //$this->helpers->write_log($key->name);
            $submenu = $key->name;
            if($key->name == 'Pague a tiempo'){ 
                $submenu = 'pague_a_tiempo';                
            }

            $this->build_menupage->add_submenu_page(
                'bc',
                __( $key->name, 'bc-textdomain' ),
                __( $key->name, 'bc-textdomain' ),
                'manage_options',
                'bc_options_' . $key->name,
                [ $this, 'controlador_display_submenu_'.$submenu ],
            );
        }

        //$parentSlug, $pageTitle, $menuTitle, $capability, $menuSlug, $functionName
        
        
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
        require_once BILL_PLUGIN_DIR_PATH . 'admin/views/index.php';
    }
    
    public function controlador_display_submenu_SIIGO(){
        require_once BILL_PLUGIN_DIR_PATH . 'admin/views/services/siigo/index.php';
       }

    public function controlador_display_submenu_ALEGRA(){
        require_once BILL_PLUGIN_DIR_PATH . 'admin/views/services/alegra/index.php';
        }

    public function controlador_display_submenu_SII(){
        require_once BILL_PLUGIN_DIR_PATH . 'admin/views/services/sii/index.php';
     }

    public function controlador_display_submenu_pague_a_tiempo(){
        require_once BILL_PLUGIN_DIR_PATH . 'admin/views/services/pasarela/index.php';
        ;
    }

    public function controlador_display_submenu_ANALITYCS(){
        require_once BILL_PLUGIN_DIR_PATH . 'admin/views/services/analitycs/index.php';
        
    }


    public function controlador_display_submenu_SUNAT(){
        require_once BILL_PLUGIN_DIR_PATH . 'admin/views/services/sunat/index.php';
        
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
            
            // antes de realizar el procedimiento de carga o actualización, verifico si existe el token
            $exist_token = $this->helpers->check_token($token);
            //$this->helpers->write_log($exist_token);


            // reviso error en status, en caso de que no exista el token
            if($exist_token['status'] != 'OK'){

                $json = json_encode([
                    'status' => 'error',
                    "result" => 'token_error'
                ]);
                echo $json;
                wp_die();            

            }else{
                // cargo y actualizo la BD con los servicios que existen en la plataforma y si están pagos
                $this->services->updateServices($exist_token['data']['plans_paids'], $exist_token['data']['plans_sistem']);

                // el token existe. Carga la data
                $row = $this->helpers->get_config_db();
                if($row){
                    $result = $this->helpers->update_config_db($token, $row->id, $exist_token['data']);
                }else{
                    $result = $this->helpers->set_config_db($token, $exist_token['data']);
                }
    
                $json = json_encode([
                    'status' => 'success',
                    "result" => $result
                ]);
                echo $json;
                wp_die();            

            }

            


        }        
    }


    /**
     * Función activación / desactivacion de servicios
     * @since  2.0.0
     * @access public
     * @author Matías
     * 
     */
    public function ajax_config_services(){
        
        $result = 0;
        $url    = '';

        check_ajax_referer( 'bc_seg', 'nonce' );        
        if( current_user_can( 'manage_options' ) ) {  
            
            extract( $_POST, EXTR_OVERWRITE ); 
            // primero reviso si el servicio es pague a tiempo o no
            //? esto porque no es un servicio que venga pagado, revisar MODAL
            $this->helpers->write_log($service);
            $this->helpers->write_log( strcasecmp($active, 'true'));

            if($service != 'PAGUE'){

                if( (strcasecmp($active, 'true')) == 0 ){
                    // primero reviso si el servicio se encuentra pago
                    $check_paid = $this->helper_service->checkServicePaid($service);
                    if($check_paid){
                        // el servicio se encuentra pago, verifico si existe el plugin
                        $exist = $this->helper_service->checkExistService($service);
                        if($exist){
                            // existe, se activa el servicio
                            $this->helper_service->ActiveServices($service, $active);
                            $show_url = 0;
                        }else{
                            // busco url del plugin
                            $url = $this->helper_service->getUrl($check_paid); 
                            $show_url = 1;
                        }
                    }                                  

                    $json = json_encode([
                        'status'   => 'success',
                        'result'   => $result,
                        'url'      => $url,
                        'paid'     => $check_paid,
                        'mode'     => 1,
                        'show_url' => $show_url // en funcion de si existe o no muestro url

                    ]);
                    echo $json;
                    wp_die();
                }else{
                    // proceso para desactivar servicio
                    $this->helper_service->processDesactiveService($service, 0);

                    $json = json_encode([
                        'status' => 'success',
                        'result' => $result,
                        'mode'   => 0,
                    ]);
                    echo $json;
                    wp_die();
                }

                  
            }else{
                // ? DUDA PAGUE A TIEMPO
            }
                   
        }  
    }


    
}







