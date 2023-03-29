<?php

/**
 * El archivo que define la clase del cerebro principal del plugin
 *
 * Una definición de clase que incluye atributos y funciones que se 
 * utilizan tanto del lado del público como del área de administración.
 * 
 * @link       http://misitioweb.com
 * @since      1.0.0
 *
 * @package    Billconnector
 * @subpackage Billconnector/includes
 */

/**
 * También mantiene el identificador único de este complemento,
 * así como la versión actual del plugin.
 *
 * @since      1.0.0
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
	 * @since    1.0.0
	 * @access   protected
	 * @var      BC_Cargador    $cargador  Mantiene y registra todos los ganchos ( Hooks ) del plugin
	 */
    protected $cargador;
    
    /**
	 * El identificador único de éste plugin
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name  El nombre o identificador único de éste plugin
	 */
    protected $plugin_name;
    
    /**
     * Versión actual del plugin
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
	 */
    public function __construct() {
        
        $this->plugin_name = 'billconnector';
        $this->version = '1.0.0';
        
        $this->cargar_dependencias();
        $this->cargar_instancias();
        $this->definir_admin_hooks();
        $this->definir_public_hooks();

        $this->cargar_wc_actions();
        
				$this->cargar_departamentos_ciudades_chile();
    }
    
    /**
	 * Cargue las dependencias necesarias para este plugin.
	 *
	 * Incluya los siguientes archivos que componen el plugin:
	 *
	 * - BC_Cargador. Itera los ganchos del plugin.
	 * - BC_i18n. Define la funcionalidad de la internacionalización
	 * - BC_Admin. Define todos los ganchos del área de administración.
	 * - BC_Public. Define todos los ganchos del del cliente/público.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    private function cargar_dependencias() {
        
        /**
		 * La clase responsable de iterar las acciones y filtros del núcleo del plugin.
		 */
        require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-cargador.php';
	
		/**
		 * La clase responsable de registrar menús y submenús
         * en el área de administración
		 */
        require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-build-menupage.php';  

		/**
		 * La clase responsable de normalizar acentos, eñes,
         * y caracteres especales
		 */
        require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-normalize.php';

		/**
		 * La clase responsable de aportar ayuda con
         * algunas tareas tediosas
		 */
        require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-helpers.php';  

		/**
		 * La clase con las funciones para las acciones de wooocommerce
		 */
        require_once BC_PLUGIN_DIR_PATH . 'includes/class-bc-wc-actions.php';  

        /**
		 * La clase responsable de definir todas las acciones en el
         * área de administración
		 */
        require_once BC_PLUGIN_DIR_PATH . 'admin/class-bc-admin.php';
        
        /**
		 * La clase responsable de definir todas las acciones en el
         * área del lado del cliente/público
		 */
        require_once BC_PLUGIN_DIR_PATH . 'public/class-bc-public.php';        
        
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
        $this->bc_public    = new BC_Public( $this->get_plugin_name(), $this->get_version() );
        
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

        $this->cargador->add_action( 'wp_ajax_bc_config', $this->bc_admin, 'ajax_config' );

        $this->cargador->add_action( 'wp_ajax_bc_search_order', $this->bc_admin, 'ajax_search_order' );
    }
    
    /**
	 * Registrar todos los ganchos relacionados con la funcionalidad del área de administración
     * Del plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    private function definir_public_hooks() {
        
		$this->cargador->add_filter('woocommerce_checkout_fields', $this->bc_public, 'checkout_fields');
		$this->cargador->add_action('woocommerce_checkout_update_order_meta', $this->bc_public, 'checkout_update_order_meta');
        $this->cargador->add_filter('woocommerce_email_order_meta_keys', $this->bc_public, 'email_order_meta_keys' );
        $this->cargador->add_action('woocommerce_admin_order_data_after_billing_address', $this->bc_public, 'admin_order_data_after_billing_address');
        $this->cargador->add_action('woocommerce_after_checkout_validation', $this->bc_public, 'after_checkout_validation', '10', 2 );

    }
    
	/**
	 * Registrar mis funciones en las acciones de woocommerce
	 */
	private function cargar_wc_actions() {
		$bc_wc_actions = new BC_Wc_Actions();
        $this->cargador->add_action( 
			'woocommerce_order_status_processing', 
			$bc_wc_actions, 
			'action_woocommerce_order_status_processing' 
		);
        $this->cargador->add_action( 
			'woocommerce_order_status_completed', 
			$bc_wc_actions, 
			'action_woocommerce_order_status_complete' 
		);
        $this->cargador->add_action( 
			'woocommerce_order_status_cancelled', 
			$bc_wc_actions, 
			'action_woocommerce_order_status_cancelled' 
		);
	}

	private function cargar_observer_user()
	{
			require_once BC_PLUGIN_DIR_PATH . 'includes/states-places.php';
	}

	/**
	 * cargar_departamentos_ciudades_chile
	 */
	private function cargar_departamentos_ciudades_chile()
	{
        require_once BC_PLUGIN_DIR_PATH . 'includes/states-places.php';
 		/**
         * Instantiate class
         */
        $GLOBALS['wc_states_places'] = new WC_States_Places_Chile(BC_FILE);

        require_once BC_PLUGIN_DIR_PATH . 'includes/filter-by-cities.php';

		add_filter( 'woocommerce_shipping_methods', function ($methods) {
			$methods['filters_by_cities_shipping_method'] = 'Filters_By_Cities_Method';
            return $methods;
		} );

        add_action( 'woocommerce_shipping_init', 'filters_by_cities_method' );

		add_filter( 'woocommerce_default_address_fields', function ($fields) {
			if ($fields['city']['priority'] < $fields['state']['priority']){
				$state_priority = $fields['state']['priority'];
				$fields['state']['priority'] = $fields['city']['priority'];
				$fields['city']['priority'] = $state_priority;
		
			}
			return $fields;
		}, 1000, 1 );
		
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
	 * La referencia a la clase que itera los ganchos con el plugin.
	 *
	 * @since     1.0.0
     * @access    public
	 * @return    BC_Cargador    Itera los ganchos del plugin.
	 */
	public function get_cargador() {
		return $this->cargador;
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
    
}
















