
<h5 class="text-sp title-modal" style="font-weight: bold;">Servicios del plugin</h5>
<hr class="divideer">

<ul class="list-group mt-2 mb-4 list-services">                           
    <li class="list-group-item list-item-services">
        <div>
            <b><h4 class="title-services">Servicios</h4></b>
        </div>
        <div class="row row-services">
        <?php 
            // listado de servicios en función a lo que el cliente pago
            if($services){
                foreach($services as $key){ ?>            
                    <div class="col-sm-4">
                        <b><p><?php echo $key['name']?></p></b>
                        <input checked type="radio" id="<?php echo 'toggle' . $key['id']?>" name="services[]" value=<?php echo $key['id']?> class="offscreen"/>
                        <label for="<?php echo 'toggle' . $key['id']?>" class="switch"></label>
                    </div>
                <?php
                }
            }
        ?>
        </div>
        
                            
    </li>
</ul>

<script src="<?php echo BILL_PLUGIN_DIR_URL . 'admin/js/bc-admin-services.js' ?>"></script>

