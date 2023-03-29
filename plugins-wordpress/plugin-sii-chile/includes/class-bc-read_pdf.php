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
class BC_ConvertFIles{

    /**
     * @author Matias
     */
    private $PATH;
	private $EXTENSION;

    public function __construct()
    {
        $this->PATH_GRAPHICS = BC_PLUGIN_DIR_PATH . 'Documents/Images/';
        $this->PATH_REPORTS  = BC_PLUGIN_DIR_PATH . 'Documents/Reports/';
        $this->EXTENSION     = '.jpg';
        $this->REPORT_END    = '.pdf';

    }

    /**
     * @author Matias
     */
    public function bc_convert_jpg_b64($jpg_encoded_64, $name_img){
        $name_p = explode(".jpg", $name_img);
        $name_img = $name_p[0];
        $base64_decode  = base64_decode($jpg_encoded_64);
        $jpg = fopen($this->PATH_GRAPHICS . $name_img . $this->EXTENSION, 'w');
        fwrite($jpg, $base64_decode);
        fclose($jpg);
        
        return ('Documents/Images/' . $name_img . $this->EXTENSION);        
    }

    /**
     * @author Matías
     */
    public function bc_convert_pdf_b64($pdf_encoded_64, $name_img){
        $name_p = explode(".jpg", $name_img);
        $name_img = $name_p[0];
        $base64_decode  = base64_decode($pdf_encoded_64);
        $pdf = fopen($this->PATH_REPORTS . $name_img . $this->REPORT_END, 'w');
        fwrite($pdf, $base64_decode);
        fclose($pdf);
        
        return ('Documents/Reports/' . $name_img . $this->REPORT_END);
    }
}