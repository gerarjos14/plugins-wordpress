<?php
/**
 * Plugin Name: LARS POS
 * Plugin URI:
 * Description: Conector POS de LARS.
 * Version: 1.0
 * Author: LARS Software Company
 * Author URI: https://lars.com.co/
 * License: GPL2
 * License URI: https://www.gnu.org/licences/gpl-2.0.html
 * Text Domain: TPV-Chelo
 */

if( ! defined('ABSPATH') ){
    die("Oh, there\'s nothing to see here.");
}

/**
 * CONSTANTES
 */
define("TABLE_KEYS", "lars_pos_keys");
define("TABLE_CATEGORIES", "lars_pos_categories");
define("TABLE_PRODUCTS", "lars_pos_products");
define("TABLE_CONFIG_SALES", "lars_pos_config_sales");
define("TABLE_CONFIG_SALES_VALUES", "lars_pos_config_sales_values");

global $wpdb;

$query = "SELECT * FROM {$wpdb->prefix}lars_pos_keys ORDER BY id ASC LIMIT 1;";
$row = $wpdb->get_row($query);

if(!(empty($row))){
    // defino por defecto en variable global la url de la API
    define("API_URL", $row->website_tpv);
    if(strpos($row->website_tpv, 'https://') === 0){
        define("API_PATH_IMAGES", $row->website_tpv . 'images/products/');
    }else{
        define("API_PATH_IMAGES", null);
    }

    // defino por defecto mediante variable global el id del almacen
    if(!(is_null($row->id_warehouse))){
        define("WAREHOUSE", $row->id_warehouse);
    }else{
        define("WAREHOUSE", '');
    }

}else{
    define("API_URL", "");
    define("ID_CATEGORIE", '');


}
define( 'lars_pos_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'lars_pos_PLUGIN_PATH', plugin_dir_path(__FILE__) );


/**
 * The code that runs during plugin activation.
 */
add_filter( 'cron_schedules', 'dcms_my_custom_schedule');
function dcms_my_custom_schedule( $schedules ) {
    // $schedules['mediaHora'] = array(
    //     'interval' => 60*30,
    //     'display' =>'mediaHora'
    //  );
    //  return $schedules;
    // cron cada diez minutos
     $schedules['ten_minutes'] = array(
        'interval' => 60*10,
        'display' =>'ten_minutes'
     );
     return $schedules;
}
register_activation_hook(__FILE__, 'lars_pos_tb_keys_create');

function lars_pos_tb_keys_create(){
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}".TABLE_KEYS." (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        id_warehouse mediumint(9),
        website_tpv varchar(255) NOT NULL,
        website varchar(255) NOT NULL,
        consumer_key varchar(255) NOT NULL,
        consumer_secret varchar(255) NOT NULL,
        attr varchar(6),
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta( $sql );
    
    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}".TABLE_CATEGORIES." (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        w_category_id mediumint(9),
        api_category_id mediumint(9) NOT NULL UNIQUE,
        increase varchar(10),
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta( $sql );
    
    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}".TABLE_PRODUCTS." (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        w_product_id mediumint(9),
        api_product_id varchar(255) NOT NULL UNIQUE,
        SKU_code varchar(255) UNIQUE,
        nombre varchar(255),
        descripcion TEXT,
        precio varchar(10),
        precio_inc_igv varchar(255),
        increase varchar(10),
        type_increase BOOLEAN DEFAULT 0,
        lumise BOOLEAN DEFAULT 0,
        categories varchar(50),
        variations varchar(255),
        image varchar(255),
        image_1 varchar(255),
        image_2 varchar(255),
        image_3 varchar(255),
        image_4 varchar(255),

        updated_at DATETIME,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta( $sql );

    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}".TABLE_PRODUCTS." (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        w_product_id mediumint(9),
        api_product_id varchar(255) NOT NULL UNIQUE,
        SKU_code varchar(255) UNIQUE,
        nombre varchar(255),
        descripcion TEXT,
        precio varchar(10),
        increase varchar(10),
        type_increase BOOLEAN DEFAULT 0,
        lumise BOOLEAN DEFAULT 0,
        categories varchar(50),
        variations varchar(255),
        image varchar(255),
        updated_at DATETIME,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta( $sql );

    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}".TABLE_CONFIG_SALES."(
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        factura varchar(255),
        impuesto varchar(255)  NOT NULL, 
        motivo_traslado varchar(255)  NOT NULL,
        peso_total int(11)  NOT NULL,
        trasbordo varchar(255)  NOT NULL,
        modalidad_transporte varchar(255)  NOT NULL,
        id_transport varchar(255)  NOT NULL,
        nombre_transportista varchar(255)  NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";    
    dbDelta( $sql );

    $sql = "INSERT INTO {$wpdb->prefix}".TABLE_CONFIG_SALES." 
        (`id`, `factura`, `impuesto`, `motivo_traslado`, `peso_total`, `trasbordo`, `modalidad_transporte`, `id_transport`, `nombre_transportista`) 
        VALUES (
            NULL,
            '0101',
            '1000',
            '1',
            '5',
            'SI',
            '01', 
            '20601243335', 
            'Perla'
        )
    ";
    dbDelta($sql);


    // creación tabla lars_pos_config_sales_values
    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}".TABLE_CONFIG_SALES_VALUES." (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        label varchar(255),
        code varchar(11), 
        categorie varchar(255),
        PRIMARY KEY (id)
    ) $charset_collate;";    
    dbDelta( $sql );

    $sql = "INSERT INTO {$wpdb->prefix}".TABLE_CONFIG_SALES_VALUES."
        (`id`, `label`, `code`, `categorie`) 
        VALUES 
        ('NULL', 'Venta interna Factura, Boletas', '0101', 'factura'),
        ('NULL', 'Venta Interna - Sustenta Gastos Deducibles Persona Natural Factura', '0112', 'factura'),
        ('NULL', 'Venta Interna-NRUS Boleta', '0113', 'factura'),
        ('NULL', 'Exportación de Bienes Factura, Boletas', '0200', 'factura'),
        ('NULL', 'Exportación de Servicios – Prestación servicios realizados íntegramente en el país Factura, Boletas', '0201', 'factura'),
        ('NULL', 'Exportación de Servicios – Prestación de servicios de hospedaje No Domiciliado Factura', '0202', 'factura'),
        ('NULL', 'Exportación de Servicios – Transporte de navieras Factura, Boletas', '0203', 'factura'),
        ('NULL', 'Exportación de Servicios – Servicios a naves y aeronaves de bandera extranjera Factura, Boletas', '0204', 'factura'),
        ('NULL', 'Exportación de Servicios - Servicios que conformen un Paquete Turístico Factura', '0205', 'factura'),
        ('NULL', 'Exportación de Servicios – Servicios complementarios al transporte de carga Factura, Boletas', '0206', 'factura'),
        ('NULL', 'Exportación de Servicios – Suministro de energía eléctrica a favor de sujetos domiciliados en ZED Factura, Boletas', '0207', 'factura'),
        ('NULL', 'Exportación de Servicios – Prestación servicios realizados parcialmente en el extranjero Factura, Boletas', '0208', 'factura'),
        ('NULL', 'Operaciones con Carta de porte aéreo (emitidas en el ámbito nacional) Factura, Boletas', '0301', 'factura'),
        ('NULL', 'Operaciones de Transporte ferroviario de pasajeros Factura, Boletas', '0302', 'factura'),
        ('NULL', 'Ventas no domiciliados que no califican como exportación Factura, Boletas', '0401', 'factura'),
        ('NULL', 'Compra interna Liquidación de compra', '0501', 'factura'),
        ('NULL', 'Anticipos Liquidación de compra', '0502', 'factura'),
        ('NULL', 'Compra de oro Liquidación de compra', '0503', 'factura'),
        ('NULL', 'Operación Sujeta a Detracción Factura, Boletas', '1001', 'factura'),
        ('NULL', 'Operación Sujeta a Detracción- Recursos Hidrobiológicos Factura, Boletas', '1002', 'factura'),
        ('NULL', 'Operación Sujeta a Detracción- Servicios de Transporte Pasajeros Factura, Boletas', '1003', 'factura'),
        ('NULL', 'Operación Sujeta a Detracción- Servicios de Transporte Carga Factura, Boletas', '1004', 'factura'),
        ('NULL', 'Operación Sujeta a Percepción Factura, Boletas', '2001', 'factura'),
        ('NULL', 'Operación sujeta a Retención de Renta de segunda categoría Factura', '2002', 'factura'),
        ('NULL', 'Créditos a empresas Factura, Boletas', '2100', 'factura'),
        ('NULL', 'Créditos de consumo revolvente Factura, Boletas', '2101', 'factura'),
        ('NULL', 'Créditos de consumo no revolvente Factura, Boletas', '2102', 'factura'),
        ('NULL', 'Otras operaciones no gravadas - Empresas del sistema financiero y cooperativas de ahorro y crédito no autorizadas a captar recursos del público Factura, Boletas', '2103', 'factura'),
        ('NULL', 'Otras operaciones no gravadas - Empresas del sistema de seguros Factura, Boletas', '2104', 'factura'),
        ('NULL', 'Comprobante emitido por AFP Boleta', '2105', 'factura'),
        ('NULL', 'Venta Nacional a Turistas - Tax Free Factura', '2106', 'factura'),
            
        ('NULL', 'IGV Impuesto General a las Ventas VAT IGV', '1000', 'impuesto'),
        ('NULL', 'Impuesto a la Venta Arroz Pilado VAT IVAP', '1016', 'impuesto'),
        ('NULL', 'ISC Impuesto Selectivo al Consumo EXC ISC', '2000', 'impuesto'),
        ('NULL', 'Impuesto a la Renta TOX IR', '3000', 'impuesto'),
        ('NULL', 'Impuesto a la bolsa plastica OTH ICBPER', '7152', 'impuesto'),
        ('NULL', 'Exportación FRE EXP', '9995', 'impuesto'),
        ('NULL', 'Gratuito FRE GRA', '9996', 'impuesto'),
        ('NULL', 'Exonerado VAT EXO', '9997', 'impuesto'),
        ('NULL', 'Inafecto FRE INA', '9998', 'impuesto'),
        ('NULL', 'Otros tributos OTH OTROS', '9999', 'impuesto'),

        ('NULL', 'Venta', '01', 'motivo_traslado'),
        ('NULL', 'Compra', '02', 'motivo_traslado'),
        ('NULL', 'Traslado entre establecimientos de la misma empresa', '04', 'motivo_traslado'),
        ('NULL', 'Importacion', '08', 'motivo_traslado'),
        ('NULL', 'Exportacion', '09', 'motivo_traslado'),
        ('NULL', 'Otros', '13', 'motivo_traslado'),
        ('NULL', 'Venta sujeta a confirmacion del comprador', '14', 'motivo_traslado'),
        ('NULL', 'Traslado emisor itinerate CP', '18', 'motivo_traslado'),
        ('NULL', 'Traslado a zona primaria', '19', 'motivo_traslado'),

        ('NULL', 'Transporte Publico', '01', 'modalidad_transporte'),
        ('NULL', 'Transporte Privado', '02', 'modalidad_transporte');

        ";
    dbDelta( $sql );


    if( ! wp_next_scheduled( 'lars_pos_cron_hook' ) ) {
        $min = time() + (60*60);
        wp_schedule_event( $min, 'twicedaily', 'lars_pos_cron_hook' );
    }
    if( ! wp_next_scheduled( 'lars_pos_cron_hook_stock' ) ) {
        $min = time() + (60*10);
        wp_schedule_event( $min, 'ten_minutes', 'lars_pos_cron_hook_stock' );
    }
}

/**
 * The code that runs during plugin deactivation.
 */
register_deactivation_hook(__FILE__, function(){
    wp_clear_scheduled_hook( 'lars_pos_cron_hook' );
    wp_clear_scheduled_hook( 'lars_pos_cron_hook_stock' );
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
    if($hook === 'toplevel_page_lars-pos'){
        wp_enqueue_script('bootstrapJs', plugins_url('assets/bootstrap/js/bootstrap.min.js', __FILE__), ['jquery']);
        wp_enqueue_style('bootstrapCSS', plugins_url('assets/bootstrap/css/bootstrap.min.css', __FILE__));
        wp_enqueue_script('signalPromo', plugins_url('assets/js/lars-pos.js', __FILE__), ['jquery']);

        wp_localize_script(
            'signalPromo',
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
 * 
 */
require_once plugin_dir_path(__FILE__) . '/vendor/autoload.php';
require_once plugin_dir_path(__FILE__) . '/includes/handle-api.php';
require_once plugin_dir_path(__FILE__) . '/includes/handle-stock.php';

