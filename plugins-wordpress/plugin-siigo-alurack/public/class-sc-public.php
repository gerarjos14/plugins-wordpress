<?php

/**
 * La funcionalidad específica de administración del plugin.
 *
 * @link       http://misitioweb.com
 * @since      1.0.0
 *
 * @package    plugin_name
 * @subpackage plugin_name/admin
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
class SC_Public {
    
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
     * @param string $plugin_name nombre o identificador único de éste plugin.
     * @param string $version La versión actual del plugin.
     */
    public function __construct( $plugin_name, $version ) {
        
        $this->plugin_name  = $plugin_name;
        $this->version      = $version;     
        
    }
    
    public function checkout_fields($fields)
    {
        $fields['billing']['billing_cedula'] = array(
            'label' => __('Cédula de ciudadanía', 'woocommerce'),
            'placeholder' => _x('Indique Cédula de ciudadanía(solo números)', 'placeholder', 'woocommerce'),
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true,
            'priority' => 25,
        );

        return $fields;
    }

    public function checkout_selects($fields){
        $fields['billing']['billing_identificacion'] = array(
			'type'     => 'select',
			'class'    => array('state_selet'),
			'label'    => _x('Elegí el tipo de identificacion', 'placeholder', 'woocommerce'),
			'required' => true,
			'options'  => array(
				'blank' => __('Elegí el tipo de identificacion'),
				'13' => __('Cédula de ciudadanía'),
				'31' => __('NIT'),
				'22' => __('Cédula de extranjería'),
				'41' => __('Pasaporte')),
            'priority' => 20,
        );
        return $fields;
    }

    public function checkout_update_order_meta($order_id)
    {
        {
            if (!empty($_POST['billing_cedula'])) {
                update_post_meta($order_id, 'Cedula', sanitize_text_field($_POST['billing_cedula']));
                update_post_meta($order_id, 'TIPO_Identificacion', $_POST['billing_identificacion']);

            }
        }
    }

    public function email_order_meta_keys($keys)
    {
        {
            $keys[] = 'Cedula';
            return $keys;
        }
    }

    public function admin_order_data_after_billing_address($order)
    {
        {
            if (get_post_meta($order->get_id(), 'Cedula', true))
                echo '<p><strong>' . __('Cedula') . ':</strong> ' . get_post_meta($order->get_id(), 'Cedula', true) . '</p>';
        }
    }

    public function after_checkout_validation($fields, $errors)
    {
        $regex = "/^[0-9]+$/";
        if(! preg_match($regex, $fields['billing_cedula'])){
            $errors->add('validation', 'La cedula ingresada no es válida');
        }

        if($fields['billing_identificacion'] == 'blank'){
            $errors->add('validation', 'Debe de seleccionar un tipo de identificación');
        }
    }


}







