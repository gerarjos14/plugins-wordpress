<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BILL_PLUGIN_DIR_URL . 'admin/css/admin-services.css' ?>">

<div class="row ">
<?php 
    // listado de servicios en función a lo que el cliente pago
    if($services){
        foreach($services as $key){ ?>            
            <div class="col-sm-6">
                <div class="card card-services">
                    <h4 class="text-sp"><b><?php echo $key['name']?></b></h4>
                    <hr class="divideer">
                    <p class="text-sp" style="font-size: 15px !important;">
                        <?php 
                            echo $key['description'];
                        ?>
                    </p>
                    <hr class="divideer">

                    <input class="offscreen btn-services-<?php if ($key['active']){ echo 'active'; }else{ echo 'desactive';} ?>" type="checkbox" id="<?php echo 'toggle' . $key['id']?>"  
                           name="services[]" value=<?php echo $key['id']?> data-toggle="toggle"
                           <?php if ($key['active']){ echo 'checked'; } ?>
                    >

                </div>
                
            </div>
        <?php
        }
    }else{ ?>
        <div class="col-sm-12">
            <div class="card text-white bg-danger card-no-plugin">
                <div class="card-body">
                    <h5 class="text-sp card-title">Error configuración</h5>
                    <p class="text-sp">
                       <b>
                           Antes de poder activar los servicios, debe cargar el token dentro de la sección 
                           de 'Configuración de BillConnector'
                       </b>
                    </p>
                </div>
            </div>
        </div>
    <?php
    }
?>
</div>

<style>
    .btn-toggle {
    top: 50%;
    transform: translateY(-50%);
  }

  .btn-primary{
        background-color: #011936 !important;
        color: #fff !important;
        border-radius: 1rem !important;
        border-color: #011936 !important;
        font-family: 'Baloo 2', sans-serif;
  }

  .toggle-off{
    background-color: #00A8E8 !important;
    color: #011936 !important;
    border-radius: 1rem !important;
    border-color: #00A8E8 !important;

    font-family: 'Baloo 2', sans-serif;

  }
  
  .toggle-off:hover,
  .btn-primary:hover{
    -webkit-box-shadow: 2px 6px 15px 0px rgb(69 65 78 / 25%) !important;
    -moz-box-shadow: 2px 6px 15px 0px rgba(69 65 78 / 25%) !important;
    box-shadow: 5px 10px 20px 1px rgb(69 65 78 / 25%) !important;
  }

  


  
</style>
<script src="<?php echo BILL_PLUGIN_DIR_URL . 'admin/js/bootstrap-toggle.js' ?>"></script>
<script src="<?php echo BILL_PLUGIN_DIR_URL . 'admin/js/bc-admin-services.js' ?>"></script>

