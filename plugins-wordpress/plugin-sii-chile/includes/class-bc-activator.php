<?php

/**
 * Se activa en la activación del plugin
 *
 * @link       https://billconnector.com
 * @since      1.0.0
 *
 * @package    Billconnector
 * @subpackage Billconnector/includes
 */

/**
 * Ésta clase define todo lo necesario durante la activación del plugin
 *
 * @since      1.0.0
 * @package    Billconnector
 * @subpackage Billconnector/includes
 * @author     BillConnector <contacto@lars.net.co>
 */
class BC_Activator {

	/**
	 * Método estático que se ejecuta al activar el plugin
	 *
	 * Creación de la tabla {$wpdb->prefix}billconnector_data
     * para guardar toda la información necesaria
	 *
	 * @since 1.0.0
     * @access public static
	 */
	public static function activate() {
		global $wpdb;
        
        
        $sql = "CREATE TABLE IF NOT EXISTS " . BC_TABLE . "(
            id int(11) NOT NULL AUTO_INCREMENT,
            token varchar(255) NOT NULL,
            unique_id varchar(255) NOT NULL,
            order_status BOOLEAN NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
            );";
        
        $wpdb->query( $sql );

        $sql2 = "CREATE TABLE IF NOT EXISTS " . BC_TABLE_ORDERS . "(
            id int(11) NOT NULL AUTO_INCREMENT,
            order_id mediumint(9) NOT NULL,
            unique_id varchar(255) NOT NULL,
            type_document enum('invoice','ballot','credit_note', 'debit_note'),
            link varchar(255),
            PRIMARY KEY (id)
            );";
                
        $wpdb->query( $sql2 );
       
        $sql3 = "CREATE TABLE IF NOT EXISTS " . BC_TABLE_LOGS . "(
            id int(11) NOT NULL AUTO_INCREMENT,
            order_id mediumint(9) NOT NULL,
            status enum('error', 'success'),
            message varchar(255),
            created_at DATETIME,
            PRIMARY KEY (id)
            );";
                
        $wpdb->query( $sql3 );

        $sql4 = "CREATE TABLE IF NOT EXISTS " . BC_TABLE_ANALITYCS . "(
            id int(11) NOT NULL AUTO_INCREMENT,
            visitor_id int,
            big_data text,
            id_usuario varchar(255),
            web_url text,
            view BOOLEAN NOT NULL DEFAULT 0,
            created_at DATETIME,
            PRIMARY KEY (id)
            );";
                    
        $wpdb->query( $sql4 );

        $sql5 = "CREATE TABLE IF NOT EXISTS ". BC_TABLE_RESULTS_ANALYTICS . "(
            id int(11) NOT NULL AUTO_INCREMENT,
            producto varchar(255),
            promedio varchar(255),
            image_producto longtext,
            pdf_producto longtext,
            bill_dice text,
            cant_usuarios int(255),
            num_ranking int(11),
            nuevos int(11),
            recurrentes int(11),
            type_recomendation varchar(255),
            legend_recomendation varchar(255),
            url_image varchar(255),
            created_at DATETIME,
            PRIMARY KEY (id)
        )";

        $wpdb->query( $sql5 ); 
        // se chequea que esté, si no está registrado, se crea el cron para el 
        // envio de email
        if( ! wp_next_scheduled( 'bill_connector_cron_hook_send_email' ) ) {
            $min = time() + (60*60);
            wp_schedule_event( $min, 'daily', 'bill_connector_cron_hook_send_email' );
        }
        
	}

   

}





