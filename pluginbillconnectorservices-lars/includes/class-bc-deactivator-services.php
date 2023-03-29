<?php

class BC_DeactivatorServices{

    
    protected $helpers;

    public function __construct()
    {
        $this->helpers = new BC_Helpers;
    }

    /**
     * Función para desactivar plugin de servicios de billconnector
     * de forma automática
     * @author Matias
     * @since 2.0.0
     * @param string service
     */
    public function desactiveServiceWP($service){
        $this->getNameServiceFolder($service);       
    }


    /**
     * En base al nombre del servicio se obtiene el nombre del slug 
     * para iniciar con el proceso de desactivación en wordpress
     * @author Matías
     * @since 2.0.0
     * @param string name
     */
    public function getNameServiceFolder($name){
        switch($name){
            case 'ALEGRA':
                if ( is_plugin_active(ALEGRA_SLUG) ) {
                    deactivate_plugins(ALEGRA_SLUG);    
                }               
            break;
            
            case 'SIIGO':
                if ( is_plugin_active(SIIGO_SLUG) ) {
                    deactivate_plugins(SIIGO_SLUG);    
                } 
            break;

            case 'SII':
                if ( is_plugin_active(SII_SLUG) ) {
                    deactivate_plugins(SII_SLUG);    
                } 
            break;

            case 'Pague a tiempo':
                // ! REVISAR SLUG DE PAGUE A TIEMPO
                // if ( is_plugin_active(ALEGRA_SLUG) ) {
                //     deactivate_plugins(ALEGRA_SLUG);    
                // } 
            break;

            case 'ANALITYCS':
                // ! REVISAR SLUG DE ANALITYCS
                // if ( is_plugin_active(ALEGRA_SLUG) ) {
                //     deactivate_plugins(ALEGRA_SLUG);    
                // } 
            break;

            case 'SUNAT':
                if ( is_plugin_active(SUNAT_SLUG) ) {
                    deactivate_plugins(SUNAT_SLUG);    
                } 
            break;


        }

    }
}