<link rel="stylesheet" href="<?php echo lars_pos_PLUGIN_DIR_URL . 'public/css/no_plugin.css' ?>">

<?php 
    if(isset($plugin[0])){ ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card text-white bg-danger card-no-plugin">
                <div class="card-body">
                    <h5 class="text-sp card-title">Debes de instalar y activar los siguientes plugins:</h5>
                    <ul>
                    <?php foreach($plugin as $key){ ?>
                       
                            <li class="text-sp"><?php echo $key;?></li> 
                        
                    <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
        
<?php  } 
?>
