<?php

if ( ! function_exists( 'is_plugin_active' ) ){
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );    
}

if (! is_plugin_active('ubigeo-peru/ubigeo-peru.php')){
    $plugin[0] = 'Ubigeo Perú';
}

if (! is_plugin_active('woocommerce/woocommerce.php')){
    $plugin[1] = 'Woocommerce';  
}

include( 'views/alert_no_plugin.php');



?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<link rel="stylesheet" href="<?php echo lars_pos_PLUGIN_DIR_URL . 'public/css/admin.css' ?>">
<link rel="stylesheet" href="<?php echo lars_pos_PLUGIN_DIR_URL . 'public/css/categories.css' ?>">
<link rel="stylesheet" href="<?php echo lars_pos_PLUGIN_DIR_URL . 'public/css/tables.css' ?>">



<div class="wrap">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="wp-heading-inline title-section-page"> <?php echo get_admin_page_title(); ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"> 
                <div class="card card-config">
                    <div class="card-body list-group list-edit" id="list-tab" role="tablist">
                        <h5 class="text-sp"><b>Configuración</b></h5>
                        
                        <hr class="divideer">
                        <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">Woocommerce</a>
                        <a class="list-group-item list-group-item-action" id="list-profile-list" data-toggle="list" href="#list-profile" role="tab" aria-controls="profile">Ventas</a>
                        <a class="list-group-item list-group-item-action" id="list-warehouses-list" data-toggle="list" href="#list-warehouses" role="tab" aria-controls="profile">Almacenes</a>

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
                                include( 'views/form_config.php');
                            ?>                        
                        </div>                
                    </div>
                    <div class="card card-config tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                        <div class="card-body">
                        <h5 class="text-sp title-modal" style="font-weight: 800;">Configuración de ventas.</h5>
                        <hr class="divideer">
                            <?php 
                                include( 'views/form_config_sale.php');
                            ?>                        
                        </div>                
                    </div>
                    <div class="card card-config tab-pane fade" id="list-warehouses" role="tabpanel" aria-labelledby="list-warehouses-list">
                        <div class="card-body">
                        <h5 class="text-sp title-modal" style="font-weight: 800;">Configuración de almacenes.</h5>
                        <hr class="divideer">
                            <?php 
                                include( 'views/form_config_warehouse.php');
                            ?>                        
                        </div>                
                    </div>
                </div>                     
            </div>     
        </div>     
    </div> 
</div>


<?php require_once(lars_pos_PLUGIN_PATH . 'includes/views/modal-config.php') ?>
<?php require_once(lars_pos_PLUGIN_PATH . 'includes/views/modal-tutorial.php') ?>

<script>
    var toggler = document.getElementsByClassName("caret");
    var i;

    for (i = 0; i < toggler.length; i++) {
    toggler[i].addEventListener("click", function() {
        this.parentElement.querySelector(".nested").classList.toggle("active");
        this.classList.toggle("caret-down");
    });
    }
</script>