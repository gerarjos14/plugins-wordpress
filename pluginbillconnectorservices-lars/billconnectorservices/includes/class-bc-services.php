<?php

class BC_Services{

    /**
	 * Objeto BC_Helpers
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      BC_Helpers
	 */
    private $helpers;
    private $des_services;

    public function __construct(){
        global $wpdb;
        $this->db      = $wpdb;
        $this->helpers = new BC_Helpers;
        $this->des_services = new BC_DeactivatorServices;
    }

    /**
     * Función services
     * Carga de servicios que se ofrecen
     * 
     * @author Matías
     * 
     */
    public function upload_services($name){                
        $this->db->insert(BC_TABLE_SERVICES, [            
            'name'   => $name,
            'paid'   => 0,
            'active' => 0,
        ]);               
    }

    /**
     * @param bool $paid
     * @param bool $active
     * @param string $name
     * @author Matias
     */
    public function updateInfoService($paid, $active, $defeated, $name){

        $this->db->update(BC_TABLE_SERVICES, [
            "paid"     => $paid,
            'defeated' => $defeated,
            "active"   => $active,
        ], 
        // condicional
        [ 
            'name' => $name 
        ]);
    }

    /**
     * Función para actualizar la data de los servicios
     * @author Matias
     */
    public function updateServices($paids, $list){
        // chequeo si hay servicios en el sistema
        $exist_service = $this->getServices();
        
        if($exist_service){
            // exsite, actualizo
            $update_service = 1;
        }else{
            // no exsiten, se crean 
            $update_service  = 0;
        }
        
        // ? DUDA revisar avá o en laravel para cambios nombres servicios
        // recorro servicios del sistema. 
        foreach($list as $key){
            // carga la data a la BD actualizo
            // contemplo por medio del switch, los cambios de nombre a mostrar
            switch($key){                
                case 'FAC_CHILE':
                    $name = 'SII';
                    if($update_service){
                        $this->updateInfoService(0, 0, 0, $name);
                    }else{
                        $this->upload_services($name);
                    }
                    break;
                case 'PAGUE_A_TIEMPO':
                    $name = 'Pague a tiempo';
                    if($update_service){
                        $this->updateInfoService(0, 0, 0, $name);
                    }else{
                        $this->upload_services($name);
                    }
                    break;
                case 'FAC_PERU':
                    $name = 'SUNAT';
                    if($update_service){
                        $this->updateInfoService(0, 0, 0, $name);
                    }else{
                        $this->upload_services($name);
                    }
                    
                    break;
                default:
                    if($update_service){
                        $this->updateInfoService(0, 0, 0, $key);
                    }else{
                        $this->upload_services($key);
                    }
                    
                
            }
        }


        // recorro servicios pagos
        foreach($paids as $key){
            $service = $key[0];
            // actualizo el servicio
            /**
             * indico que se pagó
             * indico que no está activo
             * paso nombre
             */
            $this->updateInfoService(1,0, 0, $service);
        }
    }

    /**
     * Función que se encarga de revisión por mediod de endpoint para 
     * ver que servicios se encuentran pagos
     * @author Matias
     */
    public function reviewServicesPaids(){
        
        // consulto el id del usuario
        // ! DESCOMENTAR PARA PRUEBAS O PARA PRODUCCIÓN
        $row = $this->helpers->get_config_db();
        if($row){
            $services_paids = $this->helpers->checkServices($row->user_id);
            // recorro servicios pagos
            $this->helpers->write_log('Actualización de servicios');
            foreach($services_paids as $key){

                $fecha_actual  = strtotime(date('Y-m-d', time()));
                $fecha_entrada = strtotime($key['date_end']); 
                $service = $key['platform'];

                if($fecha_actual > $fecha_entrada){
                        //$this->helpers->write_log("La fecha entrada ya ha pasado");
                        // actualizo el servicio en BD
                        $this->updateInfoService(0, 0, 1, $service);
                        // ! REVISAR DESACTIVO PLUGIN
                        $this->des_services->desactiveServiceWP($service);

                }else{
                        //$this->helpers->write_log("Aun falta algun tiempo");
                        // actualizo el servicio
                        // se procede a desactivar el servicio
                        $this->db->update(BC_TABLE_SERVICES, [                            
                            'paid' => 1,
                        ],[ 
                            'name' => $service
                        ]);
                }

                
            }
        }else{
            // no hay token ingresado, informo en debug
            $this->helpers->write_log('No hay token. reviewServicesPaids');

        }

    }

    public function getServicesDB(){
        $row = $this->helpers->getServices();
        $services = [];

        if(isset($row[0])){
            foreach($row as $key){
                
                $services[] = [
                    'id'     => $key->id,
                    'name'   => $key->name,
                    'active' => $key->active,
                    'paid'   => $key->paid
                ];
                             
            }
        }

        return($services);
    }

    public function getServicesPaids(){
        // consulto el listado de servicios pagos
        $row = $this->helpers->getServices();
        $services_to_list = [];

        if(isset($row[0])){
            foreach($row as $key){
                // si el servicio es pasarela de pagos, lo listo igual, esto se paga aparte.
                if($key->name == 'Pague a tiempo'){
                    $services_to_list[] = [
                        'id'     => $key->id,
                        'name'   => $key->name,
                        'active' => $key->active
                    ];
                }else{
                    // listo los servicios que están pagos
                    if($key->paid == 1){
                        $services_to_list[] = [
                            'id'     => $key->id,
                            'name'   => $key->name,
                            'active' => $key->active
                        ];
                    }
                }                
            }
        }

        return($services_to_list);
        
    }

    public function activeServiceDB($service, $active){

        // reviso si es para activar o no
        if($active == 'true'){

            if($service == 'PAGUE' || $service == 'ANALITYCS'){
                
                // adapto el nombre del servicio
                $name = $service == 'PAGUE' ? 'Pague a tiempo' : $service;

                // proceso a activar el servicio
                $this->db->update(BC_TABLE_SERVICES, [                            
                    'active' => 1,
                ],[ 
                    'name' => $name 
                ]);

            }else{
                // obtengo servicios diferenciados
                $diff_services = $this->getServicesDiferences();
                // recorro array de servicios diferenciados, si el nombre se encuentra, se activa.
                foreach($diff_services as $serv){
                    if($serv->name == $service){
                        // lo activo en BD
                        $this->db->update(BC_TABLE_SERVICES, [                            
                            'active' => 1,
                        ],[ 
                            'name' => $serv->name 
                        ]);
                    }else{
                        // desactivo en BD
                        $this->db->update(BC_TABLE_SERVICES, [                            
                            'active' => 0,
                        ],[ 
                            'name' => $serv->name 
                        ]);
                    }
                    
                }
                
            }
        }else{
            // se procede a desactivar el servicio
             $this->db->update(BC_TABLE_SERVICES, [                            
                'active' => 0,
            ],[ 
                'name' => $service
            ]);
        }
        
        
    }
    

    public function getServicesDiferences(){
        $query   = "SELECT name, active FROM ". BC_TABLE_SERVICES ." WHERE name !='Pague a tiempo' AND name != 'ANALITYCS' ";
        $results = $this->db->get_results( $query );
		return $results;
    }

    /**
     * Función para obtener servicios
     * @author Matias
     */
    public function getServices(){
        $query   = "SELECT * FROM ". BC_TABLE_SERVICES . "";
        $results = $this->db->get_results($query);
        return $results;
    }

    /**
     * Función para obtener servicios vencidos
     * @author Matías
     */
    public function getServicesDefeated(){
        $query   = "SELECT * FROM ". BC_TABLE_SERVICES . " WHERE defeated = 1";
        $results = $this->db->get_results($query);
        return $results;
    }

}