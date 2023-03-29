<?php

/**
  * Proporcionar una vista de área de administración para el plugin
  *
  * Este archivo se utiliza para marcar los aspectos de administración del plugin.
  *
  * @link http://misitioweb.com
  * @since desde 1.0.0
  *
  * @package Billconnector
  * @subpackage Billconnector/admin/parcials
  */
    $result                  = $this->helpers->get_config_db(); 
    $services                = $this->services->getServicesDB();
    $faq                     = $this->helpers->get_faq();
    $check_services_defeated = $this->services->getServicesDefeated();


?>

<link href='https://fonts.googleapis.com/css?family=Baloo+2' rel='stylesheet'>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<link rel="stylesheet" href="<?php echo BILL_PLUGIN_DIR_URL . 'admin/css/index.css' ?>">
<link rel="stylesheet" href="<?php echo BILL_PLUGIN_DIR_URL . 'admin/css/admin-panel.css' ?>">
<link rel="stylesheet" href="<?php echo BILL_PLUGIN_DIR_URL . 'admin/css/configuration.css' ?>">

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


<div class="wrap">
    <div class="container">
        <?php
            if($check_services_defeated){
                include('messages/error.php');
            }
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-title-plugin">
                    <div class="row">
                        <div class="col-sm-3 col-container-logo" style="max-width: none !important;">
                            <img class="bill-logo" src="<?php echo BILL_PLUGIN_DIR_URL . 'public/images/Asset-2.png' ?>" alt="">
                        </div>
                        <div class="col-sm-6">
                            <h1 class="wp-heading-inline title-section-page">BillConnector</h1>
                        </div>
                        <div class="col-sm-3 col-btn-perfil" >
                        <a href="" class=" btn btn-bc">Visitar perfil</a>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"> 
                <div class="card card-config">
                    <div class="card-body list-group list-edit" id="list-tab" role="tablist">
                        <h5 class="text-sp"><b>Configuración</b></h5>
                        
                        <hr class="divideer">
                        <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">Configuración BillConnector</a>
                        <a class="list-group-item list-group-item-action nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Servicios</a>
                        <a class="list-group-item list-group-item-action" id="list-warehouses-list" data-toggle="list" href="#list-warehouses" role="tab" aria-controls="profile">Ayuda</a>

                    </div>                
                </div>
            </div> 

            <div class="col-md-8">           
                <div class="tab-content" id="nav-tabContent">
                    <div class="card card-config tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                        <div class="card-body">
                        <h5 class="text-sp title-modal" style="font-weight: bold;">Configuración del plugin</h5>
                        <hr class="divideer">
                            <?php 
                                include( 'configuration/form_config_plugin.php');
                            ?>                                
                        </div>                
                    </div>

                    <div class="card card-config tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card-body">
                            <h5 class="text-sp title-modal" style="font-weight: bold;">Servicios</h5>
                            <hr class="divideer">
                            <?php 
                                include( 'services/index.php');
                            ?>                                
                        </div>
                    </div>


                    

                    <div class="card card-config tab-pane fade" id="list-warehouses" role="tabpanel" aria-labelledby="list-warehouses-list">
                        <div class="card-body">
                        <h5 class="text-sp title-modal" style="font-weight: 800;">Ayuda</h5>
                        <hr class="divideer">
                            <?php 
                                include( 'faq/index.php');
                            ?>                        
                        </div>                
                    </div>
                </div>                     
            </div>     
        </div>     
    </div> 
</div>