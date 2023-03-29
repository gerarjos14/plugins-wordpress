<?php

/**
 * Se activa en la activación del plugin
 *
 * @link       https://billconnector.com
 * @since      2.0.0
 *
 * @package    Billconnector
 * @subpackage Billconnector/includes
 */

/**
 * Ésta clase define todo lo necesario durante la activación del plugin
 *
 * @since      2.0.0
 * @package    Billconnector
 * @subpackage Billconnector/includes
 * @author     BillConnector <contacto@lars.net.co>
 */
class BC_Activator
{
    /**
	 * El cargador que es responsable de mantener y registrar
     * todos los ganchos (hooks) que alimentan el plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      BC_Services    $cargador  Mantiene y registra todos los ganchos ( Hooks ) del plugin
	 */
    protected $services;

    public function __construct() {

        require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-services.php';  
        require_once BILL_PLUGIN_DIR_PATH . 'includes/class-bc-faq.php';  

    }

    /**
     * Método estático que se ejecuta al activar el plugin
     *
     * Creación de la tabla {$wpdb->prefix}billconnector_data
     * para guardar toda la información necesaria
     *
     * @since 2.0.0
     * @access public static
     */
    public static function activate()
    {

        global $wpdb;

        // Registro de tabla general users
        $sql = "CREATE TABLE IF NOT EXISTS " . BC_TABLE_CONFIG_GENERAL . "(
            id int(11) NOT NULL AUTO_INCREMENT,
            token varchar(255) NOT NULL,
            country varchar(255),
            type varchar(255),
            user_id int(11),            
            PRIMARY KEY (id)
            );";

        $wpdb->query($sql);

        $sql_services = "CREATE TABLE IF NOT EXISTS " . BC_TABLE_SERVICES . "(
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description TEXT, 
            paid BOOLEAN NOT NULL DEFAULT 0,
            active BOOLEAN NOT NULL DEFAULT 0,
            defeated BOOLEAN NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
            );";

        $wpdb->query($sql_services);

        $sql_faq = "CREATE TABLE IF NOT EXISTS " . BC_TABLE_FAQ . "(
            id int(11) NOT NULL AUTO_INCREMENT,
            pregunta_faq varchar(255) NOT NULL,
            answer TEXT,            
            PRIMARY KEY (id)
            );";

        $wpdb->query($sql_faq);

        // Fin registro tabla general 

       // seeder preguntas
       $faq = new BC_FAQ;
       $faq->seeder();
        

        /** ----------- REGISTRO DE CRONS ----------*/
        
        // cron para realizar censo de servicios pagos en sistema
        if( ! wp_next_scheduled( 'bill_connector_cron_hook_paid_services_review' ) ) {
            wp_schedule_event( time(), 'every_minute', 'bill_connector_cron_hook_paid_services_review' );
        }

        /** ----------- REGISTRO DE CRONS ----------*/


    }   


    
}
