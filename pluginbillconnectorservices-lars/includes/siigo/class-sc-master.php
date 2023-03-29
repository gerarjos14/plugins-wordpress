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
 * @package    Siigo_Connector
 * @subpackage Siigo-Connector/includes
 */

/**
 * También mantiene el identificador único de este complemento,
 * así como la versión actual del plugin.
 *
 * @since      1.0.0
 * @package    Siigo_Connector
 * @subpackage Siigo-Connector/includes
 * @author     Gilbert Rodríguez <email@example.com>
 * 
 * @property object $cargador
 * @property string $plugin_name
 * @property string $version
 */
class SC_Master {
    
    /**
	 * El cargador que es responsable de mantener y registrar
     * todos los ganchos (hooks) que alimentan el plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SC_Cargador    $cargador  Mantiene y registra todos los ganchos ( Hooks ) del plugin
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
        
        $this->plugin_name = 'siigo_connector';
        $this->version = '1.0.0';
        
        $this->cargar_dependencias();
        $this->cargar_instancias();
        $this->set_idiomas();
        $this->definir_admin_hooks();
        $this->definir_public_hooks();
        $this->cargar_cron();
        $this->cargar_wc_actions();

		$this->cargar_departamentos_ciudades_colombia();
    }
    
    /**
	 * Cargue las dependencias necesarias para este plugin.
	 *
	 * Incluya los siguientes archivos que componen el plugin:
	 *
	 * - SC_Cargador. Itera los ganchos del plugin.
	 * - SC_i18n. Define la funcionalidad de la internacionalización
	 * - SC_Admin. Define todos los ganchos del área de administración.
	 * - SC_Public. Define todos los ganchos del del cliente/público.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    private function cargar_dependencias() {
        /**
		 * autoload de composer 
		 */
		require_once SC_PLUGIN_DIR_PATH . 'vendor/autoload.php';


        /**
		 * La clase responsable de iterar las acciones y filtros del núcleo del plugin.
		 */
        require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-cargador.php';
        
        /**
		 * La clase responsable de definir la funcionalidad de la
         * internacionalización del plugin
		 */
        require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-i18n.php';        
  		
		/**
		 * La clase responsable de registrar menús y submenús
         * en el área de administración
		 */
        require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-build-menupage.php';        

		/**
		 * La clase responsable de normalizar acentos, eñes,
         * y caracteres especales
		 */
        require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-normalize.php';

		/**
		 * La clase responsable de aportar ayuda con
         * algunas tareas tediosas
		 */
        require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-helpers.php';  

		/**
		 * La clase responsable de realizar las consultas 
         * externas
		 */
        require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-wpremote.php';       
		
		/**
		 * La clase responsable de Cron
		 */
        require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-cron.php';    

		/**
		 * La clase con las funciones para las acciones de wooocommerce
		 */
        require_once SC_PLUGIN_DIR_PATH . 'includes/class-sc-wcactions.php';      

		/**
		 * La clase responsable de definir todas las acciones en el
         * área de administración
		 */
        require_once SC_PLUGIN_DIR_PATH . 'admin/class-sc-admin.php';

		/**
		 * La clase responsable de definir todas las acciones en el
         * área del lado del cliente/público
		 */
        require_once SC_PLUGIN_DIR_PATH . 'public/class-sc-public.php';
    }
    
    /**
	 * Defina la configuración regional de este plugin para la internacionalización.
     *
     * Utiliza la clase SC_i18n para establecer el dominio y registrar el gancho
     * con WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    private function set_idiomas() {
        
        $sc_i18n = new SC_i18n();
        $this->cargador->add_action( 'plugins_loaded', $sc_i18n, 'load_plugin_textdomain' );        
        
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
        $this->cargador     = new SC_cargador;
        $this->sc_admin     = new SC_Admin( $this->get_plugin_name(), $this->get_version() );
        $this->sc_public    = new SC_Public( $this->get_plugin_name(), $this->get_version() );
    }
    
	/**
	 * Registrar todo lo relacionado con cron
	 */
	private function cargar_cron() {
		// Instancia de manejador del cron
		$sc_cron = new SC_cron();
        $this->cargador->add_filter( 'cron_schedules', $sc_cron, 'intervals' );        
        $this->cargador->add_action( 'sc_get_token', $sc_cron, 'get_token_remote' );
        $this->cargador->add_action( 'sc_get_products', $sc_cron, 'get_products' );
        $this->cargador->add_action( 'sc_woocommerce_product', $sc_cron, 'store_and_update_woocommerce_product' );
        $this->cargador->add_action( 'init', $sc_cron, 'initializer' );
	}


	/**
	 * Registrar mis funciones en las acciones de woocommerce
	 */
	private function cargar_wc_actions() {
		$sc_wcactions = new SC_Wcactions();
		
        $this->cargador->add_action( 
			'woocommerce_order_status_processing', 
			$sc_wcactions, 
			'action_woocommerce_order_status_processing' 
		);
        $this->cargador->add_action( 
			'woocommerce_order_status_completed',
			$sc_wcactions, 
			'action_woocommerce_order_status_complete' 
		);
	}

	
    /**
	 * Registrar todos los ganchos relacionados con la funcionalidad del área de administración
     * Del plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    private function definir_admin_hooks() {
        
        $this->cargador->add_action( 'admin_enqueue_scripts', $this->sc_admin, 'enqueue_styles' );
        $this->cargador->add_action( 'admin_enqueue_scripts', $this->sc_admin, 'enqueue_scripts' );

		$this->cargador->add_action( 'admin_menu', $this->sc_admin, 'add_menu' );

        $this->cargador->add_action( 'wp_ajax_sc_keys', $this->sc_admin, 'ajax_keys' );

    }
    
	/**
	 * Registrar todos los ganchos relacionados con la funcionalidad del área de administración
     * Del plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    private function definir_public_hooks() {
        
		$this->cargador->add_filter('woocommerce_checkout_fields', $this->sc_public, 'checkout_fields');
		$this->cargador->add_filter('woocommerce_checkout_fields', $this->sc_public, 'checkout_selects');
		$this->cargador->add_action('woocommerce_checkout_update_order_meta', $this->sc_public, 'checkout_update_order_meta');
        $this->cargador->add_filter('woocommerce_email_order_meta_keys', $this->sc_public, 'email_order_meta_keys' );
        $this->cargador->add_action('woocommerce_admin_order_data_after_billing_address', $this->sc_public, 'admin_order_data_after_billing_address');
        $this->cargador->add_action('woocommerce_after_checkout_validation', $this->sc_public, 'after_checkout_validation', '10', 2 );

    }

	/**
	 * cargar_departamentos_ciudades_colombia
	 */
	private function cargar_departamentos_ciudades_colombia()
	{
		load_plugin_textdomain('siigo-connector-textdomain',
        FALSE, dirname(plugin_basename(SC_FILE)) . '/languages');

        require_once SC_PLUGIN_DIR_PATH . 'includes/states-places.php';
 		/**
         * Instantiate class
         */
        $GLOBALS['wc_states_places'] = new WC_States_Places_Colombia(SC_FILE);

        require_once SC_PLUGIN_DIR_PATH . 'includes/filter-by-cities.php';

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
	 * @return    SC_Cargador    Itera los ganchos del plugin.
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
















