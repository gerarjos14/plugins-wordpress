<?php

/**
 * La funcionalidad específica de administración del plugin.
 *
 * @link       https://billconnector.com
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
 * @package    Billconnector
 * @subpackage Billconnector/admin
  * @author     BillConnector <contacto@lars.net.co>
 * 
 * @property string $plugin_name
 * @property string $version
 */
class BC_Public {
    
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
        $fields['billing']['billing_giro'] = array(
            'label' => __('Giro', 'woocommerce'),
            'placeholder' => _x('Indique Giro', 'placeholder', 'woocommerce'),
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true,
            'priority' => 4,
        );
        $fields['billing']['billing_rut'] = array(
            'label' => __('Rut', 'woocommerce'),
            'placeholder' => _x('Indique Rut', 'placeholder', 'woocommerce'),
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true,
            'priority' => 3,
        );
        $fields['billing']['billing_first_name'] = array(
            'label' => __('Razon Social (Nombre)', 'woocommerce'),
            'placeholder' => _x('Indique Razon Social', 'placeholder', 'woocommerce'),
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true,
            'priority' => 1,
        );
        $fields['billing']['billing_type'] = array(
            'label'       => __('Tipo de Comprobante', 'woocommerce'),
            'placeholder' => _x('', 'placeholder', 'woocommerce'),
            'required'    => true,
            'clear'       => false,
            'type'        => 'select',
            'priority' => 2,
            'options'     => array(
                'ballot' => __('Boleta Electronica', 'woocommerce' ),
                'invoice' => __('Factura Electronica', 'woocommerce' )
                )
            );     
      
        unset( $fields['billing']['billing_last_name'] );
        unset( $fields['billing']['billing_company'] ); // remove company fie
 // remove company fie

        return $fields;
    }

    public function checkout_update_order_meta($order_id)
    {
        {
            if (!empty($_POST['billing_rut'])) {
                update_post_meta($order_id, 'Rut', sanitize_text_field($_POST['billing_rut']));
            }
            if (!empty($_POST['billing_giro'])) {
                update_post_meta($order_id, 'Giro', sanitize_text_field($_POST['billing_giro']));
            }
            if (!empty($_POST['billing_type'])) {
                update_post_meta($order_id, 'Type', sanitize_text_field($_POST['billing_type']));
            }
        }
    }

    public function email_order_meta_keys($keys)
    {
        {
            $keys[] = 'Rut';
            return $keys;
        }
    }

    public function admin_order_data_after_billing_address($order)
    {
        {
            if (get_post_meta($order->get_id(), 'Rut', true))
                echo '<p><strong>' . __('Rut') . ':</strong> ' . get_post_meta($order->get_id(), 'Rut', true) . '</p>';
        }
        {
            if (get_post_meta($order->get_id(), 'Giro', true))
                echo '<p><strong>' . __('Giro') . ':</strong> ' . get_post_meta($order->get_id(), 'Giro', true) . '</p>';
        }
        {
            if (get_post_meta($order->get_id(), 'Type', true))
                echo '<p><strong>' . __('Type') . ':</strong> ' . get_post_meta($order->get_id(), 'Type', true) . '</p>';
        }
    }
   

    public function after_checkout_validation($fields, $errors)
    {
        $r = strtoupper(preg_replace('/[^k0-9]/i', '', $fields['billing_rut']));
        $sub_rut = substr($r, 0, strlen($r) - 1);
        $sub_dv = substr($r, -1);
        $x = 2;
        $s = 0;
        for ($i = strlen($sub_rut) - 1; $i >= 0; $i--) {
            if ($x > 7) {
                $x = 2;
            }
            $s += $sub_rut[$i] * $x;
            $x++;
        }
        $dv = 11 - ($s % 11);
        if ($dv == 10) {
            $dv = 'K';
        }
        if ($dv == 11) {
            $dv = '0';
        }


        if ($dv != $sub_dv) {
            $errors->add('validation', 'El rut ingresado no es válido');
        }
    }
}







