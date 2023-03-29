<?php

/**
 * Se activa en la activación del plugin
 *
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
class SC_Activator {

	/**
	 * Método estático que se ejecuta al activar el plugin
	 *
	 * Creación de la tabla {$wpdb->prefix}beziercode_data
         * para guardar toda la información necesaria
	 *
	 * @since 1.0.0
         * @access public static
	 */
	public static function activate() {

        global $wpdb;
        
        $sql = "CREATE TABLE IF NOT EXISTS " . SC_TABLE . "(
        id int(11) NOT NULL AUTO_INCREMENT,
        website varchar(100) NOT NULL,
        username varchar(100) NOT NULL,
        access_key varchar(100) NOT NULL,
        consumer_key varchar(255) NOT NULL,
        consumer_secret varchar(255) NOT NULL,
        access_token text,
        PRIMARY KEY (id)
        );";
        
        $wpdb->query( $sql );
        
        $sql2 = "CREATE TABLE IF NOT EXISTS " . SC_TABLE_PRODUCTS . "(
        id int(11) NOT NULL AUTO_INCREMENT,
        siigo_id varchar(255) NOT NULL UNIQUE,
        code varchar(255) NOT NULL,
        woocommerce_id mediumint(9),
        name varchar(255),
        price varchar(10),
        stock_control BOOLEAN DEFAULT 0,
        stock_quantity mediumint(9),
        description TEXT,
        updated_at DATETIME,
        PRIMARY KEY (id)
        );";
                
        $wpdb->query( $sql2 );

		$sql3 = "CREATE TABLE IF NOT EXISTS " . SC_TABLE_CUSTOMERS . "(
		id int(11) NOT NULL AUTO_INCREMENT,
		siigo_id varchar(255) NOT NULL UNIQUE,
		woocommerce_id mediumint(9),
		identification varchar(255),
		first_name varchar(255),
		last_name varchar(255),
		phone varchar(255),
		address varchar(255),
		state_code varchar(10),
		city_code varchar(10),
		email varchar(150),
		PRIMARY KEY (id)
		);";
				
		$wpdb->query( $sql3 );
	}

}





