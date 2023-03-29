<?php

require_once plugin_dir_path(__DIR__) . 'function-back-config-sales.php';
require_once plugin_dir_path(__DIR__) . 'functions-stock.php';

    global $wpdb;
    // valores configuracion del plugin
    $query = "SELECT * FROM {$wpdb->prefix}lars_pos_keys ORDER BY id ASC LIMIT 1;";
    $config_sales = $wpdb->get_row( $query );
    if(empty($config_sales->website_tpv)){ 
        include( 'error/no_data_basic.php');
        ?>
        
<?php
    }else{    
        // como están cargadas las URL de la página TPV armo la consulta para traer la data de los almacenes
        $data_warehouse = getDataWarehouse();
        
        
?>
        <form method="POST" >
            <div class="modal-body">
                <h5 class="text-sp" style="font-size: 1.1rem; font-weight: 800">Selecciona el almacen para hacer las importaciones</h5>
                <?php
                    // verifico si hay un almacen registrado en la BD, en caso de que sí, busco en el TPV para mostrar la data actual 
                    // del almacen seleccionado.
                    if(! is_null($config_sales->id_warehouse)){
                        // busco la data del almacen
                        $warehouse = getDataActualWarehouse($config_sales->id_warehouse); ?>
                        <p class="text-sp" style="font-size: 15px;" >
                            <b>Almacen seleccionado: <?php echo $warehouse->name; ?>  
                            </b>
                        </p>
                    <?php 

                    }
                ?>
                <table class="form-table p-2">  
                    <tr>
                        <th class="row-title text-sp">
                            <label for="factura">Almacenes</label>
                        </th>
                        

                        <td>
                            <select class="regular-text form-control form-control-tpv" name="warehouse" id="id_warehouse">
                            <?php 
                            foreach($data_warehouse as $key){ 
                                if($key->id == $config_sales->id_warehouse){
                            ?>
                                <option selected value="<?php echo $key->id; ?>">  <?php echo $key->name; ?> </option>

                            <?php
                                }else{ ?>
                                    <option value="<?php echo $key->id; ?>">  <?php echo $key->name; ?> </option>
                            <?php
                                }
                            ?>
                            <?php }?>
                            </select>
                                

                        </td>
                    </tr>    
                    

                </table>

            </div>
            <hr class="divideer">
            <div class="row">                
                <div class="col-md-6 col-container-button">
                    <button id="sendWarehouseForm" type="button" class="btn btn-bc btn-lg btn-block"><b>Guardar cambios</b></button>
                </div>        
            </div>
        </form>

<?php 
    }

?>