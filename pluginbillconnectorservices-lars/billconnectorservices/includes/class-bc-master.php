<?php

/**
 * @link
 * @since
 * 
 * @package BillConnector
 * @subpackage BillConnector/includes
 */

 /**
 * También mantiene el identificador único de este complemento,
 * así como la versión actual del plugin.
 *
 * @since      2.0.0
 * @package    Billconnector
 * @subpackage Billconnector/includes
 * @author     BillConnector <contacto@lars.net.co>
 * 
 * @property object $cargador
 * @property string $plugin_name
 * @property string $version
 */

class BC_Master {
    
    /**
	 * El cargador que es responsable de mantener y registrar
     * todos los ganchos (hooks) que alimentan el plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      BC_Cargador    $cargador  Mantiene y registra todos los ganchos ( Hooks ) del plugin
	 */
    protected $cargador;
    
    /**
	 * El identificador único de éste plugin
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name  El nombre o identificador único de éste plugin
	 */
    protected $plugin_name;
    
    /**
     * Versión actual del plugin
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version  La versión actual del plugin
	 */
    protected $version;
    
    /**
     * Constructor
     * 
	 * Defina la funcionalidad principal del plugin.
	 *
	 * Establece el nombre y la versión del plugin que se puede utilizar en todo el plugin.
     * Cargar las dependencias, carga de instancias, definir la configuración regional (idioma)
     * Establecer los ganchos para el área de administración y
     * el lado público del sitio.
	 *
	 * @since    2.0.0
	 */
    public function __construct() {
        
        $this->plugin_name = 'Billconnector';
        $this->version = '2.0.0';
        
        $this->cargar_dependencias();
        $this->cargar_instancias();
        $this->definir_admin_hooks();
        
    }

    private function cargar_dependencias(){
       
		/**
		 * La clase responsable de iterar las acciones y filtros del núcleo del plugin.
		 */
        require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-cargador.php';
		/**
		 * La clase responsable de registrar menús y submenús
         * en el área de administración
		 */
        require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-build-menupage.php';  

		

		/**
		 * La clase responsable de aportar ayuda con
         * algunas tareas tediosas
		 */
        require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-helpers.php';  

		
		/**
		 * Servicios
		 */
		require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-services.php';  

		/**
		 * Helper de servicios
		 */
		require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-helpers-services.php';  


        /**
		 * La clase responsable de definir todas las acciones en el
         * área de administración
		 */
		require_once BILL_PLUGIN_DIR_PATH . 'admin/class-bc-admin.php';


		require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-faq.php';

		require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-deactivator-services.php';
		

       
    }

    /**
	 * Cargar todas las instancias necesarias para el uso de los 
     * archivos de las clases agregadas
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    private function cargar_instancias() {
        
        // Cree una instancia del cargador que se utilizará para registrar los ganchos con WordPress.
        $this->cargador     = new BC_cargador;
        $this->bc_admin     = new BC_Admin( $this->get_plugin_name(), $this->get_version() );
        
    }

    /**
	 * El nombre del plugin utilizado para identificarlo de forma exclusiva en el contexto de
     * WordPress y para definir la funcionalidad de internacionalización.
	 *
	 * @since     1.0.0
     * @access    public
	 * @return    string    El nombre del plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

    /**
	 * Retorna el número de la versión del plugin
	 *
	 * @since     1.0.0
     * @access    public
	 * @return    string    El número de la versión del plugin.
	 */
	public function get_version() {
		return $this->version;
	}


    /**
	 * Registrar todos los ganchos relacionados con la funcionalidad del área de administración
     * Del plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    private function definir_admin_hooks() {
        
        $this->cargador->add_action( 'admin_enqueue_scripts', $this->bc_admin, 'enqueue_styles' );
        $this->cargador->add_action( 'admin_enqueue_scripts', $this->bc_admin, 'enqueue_scripts' );

		$this->cargador->add_action( 'admin_menu', $this->bc_admin, 'add_menu' );
		
		/** hook para la carga de token, formulario de configuracion */
        $this->cargador->add_action( 'wp_ajax_bc_config', $this->bc_admin, 'ajax_config' );
        $this->cargador->add_action( 'wp_ajax_bc_config_services', $this->bc_admin, 'ajax_config_services' );


    }

    


	/**
	 * Ejecuta el cargador para ejecutar todos los ganchos con WordPress.
	 *
	 * @since    1.0.0
     * @access   public
	 */
    public function run() {
        $this->cargador->run();
    }

	
}