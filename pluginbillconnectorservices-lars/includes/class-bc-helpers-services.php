<?php



class BC_Helpers_Services{

    /**
	 * Objeto wpdb
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      object    $db @global $wpdb
	 */
    private $db;
    private $helper;
    private $services;
    private $des_services;
	
    public function __construct() {
        global $wpdb;
        $this->db           = $wpdb;
        $this->helper       = new BC_Helpers;
        $this->services     = new BC_Services;
        $this->des_services = new BC_DeactivatorServices;

    }

    public function checkServicePaid($name){
        // obtengo informacion del servicio
        $query  = "SELECT * FROM " . BC_TABLE_SERVICES . " WHERE name = '". $name . "'";
        $result = $this->db->get_row($query);
        // verifico que exista
        if($result){
            // verifico que esté pago
            if($result->paid){
                return 1;
            }else{
                return 0;
            }
        }else{
            return -1;
        }
        
    }
    
    /**
     * Función para obtener url relacionada con el servicio
     * @author Matías
     * @param bool $is_paid
     * 
     */
    public function getUrl($is_paid){
        // !REVISAR
        // revisar endpoint para obtener
        $url = '';
        return $url;
    }

    public function checkServicesActive(){
        //* excluyo los servicios de IA y pasarela ya que estos pueden convivir con otros servicios
        $query  = "SELECT name FROM " . BC_TABLE_SERVICES . " WHERE active  = 1 AND name != 'Pague a tiempo' AND name != 'ANALITYCS' ";
        $result = $this->db->get_results($query);
        return $result;
    }

    /**
     * Función inicio activación, desactivación hooks
     * @author Matias
     * @since 2.0.0
     */
    public function ActiveServices($service, $active){   
        // activo servicio, plugin en wordpress
        $this->activeServiceWP($service);
        // marco en BD que está activo
        $this->services->activeServiceDB($service, 'true');
        
    }

    /**
     * función para desactivar servicio
     * @author Matias
     * @param string $service
     * @param bool $active
     * @since 2.0.0
     * 
     */
    public function processDesactiveService($service, $active){
        // marco que está desactivado en BD
        $this->services->activeServiceDB($service, 'false');
        $this->des_services->desactiveServiceWP($service);
        $this->helper->write_log($service . ' desactivado');
    }

    /**
     * Función para activar servicio
     * @author Matias
     * @since 2.0.0
     */
    public function activeServiceWP($name){
        $this->helper->write_log($name . ' activado');

        switch($name){
            case 'SIIGO':
                if(!(is_plugin_active(SIIGO_SLUG))){
                    activate_plugin(SIIGO_SLUG);
                }
                $this->helper->write_log($name . ' activado');
                break;
            case 'ALEGRA':
                // activación de ALEGRA                
                if(!(is_plugin_active(ALEGRA_SLUG))){
                    activate_plugin(ALEGRA_SLUG);
                }

                $this->helper->write_log($name . ' activado');
                break;

            case 'SII':
                // empieza activación de SII
                if(!(is_plugin_active(SII_SLUG))){
                    activate_plugin(SII_SLUG);
                }
                $this->helper->write_log($name . ' activado');
                break;

            case 'Pague a tiempo':
                
                $this->helper->write_log($name . ' activado');
                break;
            case 'ANALITYCS':
                $this->helper->write_log($name . ' activado');
                break;

            case 'SUNAT':
                if(!(is_plugin_active(SUNAT_SLUG))){
                    activate_plugin(SUNAT_SLUG);
                }
                $this->helper->write_log($name . ' activado');
                break;
        }
    }



    /**
     * Verifico si exsite el plugin o no
     * @author
     */
    public function checkExistService($name){
        switch($name){
            case 'SIIGO':
                if ( $this->check_plugin_installed( SIIGO_SLUG ) ) {
                    return true;
                }            
                return false;

            break;
            case 'ALEGRA':
                if ( $this->check_plugin_installed( ALEGRA_SLUG ) ) {
                    return true;
                }            
                return false;
                
            break;

            case 'SII':
                if ( $this->check_plugin_installed( SII_SLUG ) ) {
                    return true;
                }            
                return false;
            break;

            case 'Pague a tiempo':
                // if ( $this->check_plugin_installed( ALEGRA_SLUG ) ) {
                //     return true;
                // }  
                          
                // return false;
            break;
            case 'ANALITYCS':
                // if ( $this->check_plugin_installed( ALEGRA_SLUG ) ) {
                //     return true;
                // }            
                // return false;
            break;

            case 'SUNAT':
                if ( $this->check_plugin_installed( SUNAT_SLUG ) ) {
                    return true;
                }            
                return false;
            break;
                      
        }
    }

    public function check_plugin_installed( $plugin_slug ): bool {
        $installed_plugins = get_plugins();
    
        return array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, true );
    }
    
}