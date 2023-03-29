<?php

require_once plugin_dir_path(__DIR__) . 'function-back-config-sales.php';

    global $wpdb;
    // valores configuracion del plugin
    $query = "SELECT * FROM {$wpdb->prefix}lars_pos_config_sales ORDER BY id ASC LIMIT 1;";
    $config_sales = $wpdb->get_row( $query );
    
    $data_facturas = getFacturas();
    $data_impuestos= getImpuestos();
    $data_motivo = getMotivo();
    $data_transporte = getModalidad();
    
?>


<form method="POST" >
    <div class="modal-body">
        <h5 class="text-sp" style="font-size: 1.1rem; font-weight: 800">Impuestos</h5>
        <table class="form-table p-2">  
            <tr>
                <th class="row-title text-sp">
                    <label for="factura">Factura</label>
                </th>
                <td>
                    <select class="regular-text form-control form-control-tpv" name="factura" id="id_factura">
                        <?php foreach($data_facturas as $key){ 
                            if($key['default']){?>
                                <option value="<?php echo $key['code'];?>">  <?php echo $key['label'];?> </option>
                        <?php
                            } else{ ?>
                                <option value="<?php echo $key['code'];?>">  <?php echo $key['label'];?> </option>
                        <?php
                            }                        
                        ?>
                            
                        <?php }?>
                    </select>

                    <!-- <input autocomplete="off" placeholder="Factura" id="id_factura" name="factura" class="regular-text form-control form-control-tpv" type="text" 
                    value="<?php if($config_sales){echo $config_sales->factura;}?>"> -->
                </td>
            </tr>    
            <tr>
                <th class="row-title text-sp">
                    <label for="id_impuesto">Impuesto</label>
                </th>
                <td>
                <select class="regular-text form-control form-control-tpv" id="id_impuesto" name="impuesto">
                    <?php foreach($data_impuestos as $key){ 
                        if($key['default']){?>
                            <option value="<?php echo $key['code'];?>">  <?php echo $key['label'];?> </option>
                    <?php
                        } else{ ?>
                            <option value="<?php echo $key['code'];?>">  <?php echo $key['label'];?> </option>
                    <?php
                        }                        
                    ?>
                        
                    <?php }?>
                </select>
                </td>
            </tr>          
            <tr>
                <th class="row-title text-sp">
                    <label for="id_motivo_traslado">Motivo de traslado</label>
                </th>
                <td>
                    <select class="regular-text form-control form-control-tpv" id="id_motivo_traslado" name="motivo_traslado">
                        <?php foreach($data_motivo as $key){ 
                            if($key['default']){?>
                                <option value="<?php echo $key['code'];?>">  <?php echo $key['label'];?> </option>
                        <?php
                            } else{ ?>
                                <option value="<?php echo $key['code'];?>">  <?php echo $key['label'];?> </option>
                        <?php
                            }                        
                        ?>
                            
                        <?php }?>
                    </select>                    
                </td>
            </tr>

            <tr>
                <th class="row-title text-sp">
                    <label for="id_peso_total">Peso total</label>
                </th>
                <td>
                    
                    <input autocomplete="off" placeholder="Peso total" id="id_peso_total" name="peso_total" class="regular-text form-control form-control-tpv" type="text" 
                    value="<?php if($config_sales){echo $config_sales->peso_total;}?>"
                    >
                </td>
            </tr>

            <tr>
                <th class="row-title text-sp">
                    <label for="id_trasbordo">Trasbordo</label>
                </th>
                <td>
                    
                    <input autocomplete="off" placeholder="Trasbordo" id="id_trasbordo" name="trasbordo" class="regular-text form-control form-control-tpv" type="text" 
                    value="<?php if($config_sales){echo $config_sales->trasbordo;}?>"
                    >
                </td>
            </tr>
        </table>

        <h5 class="text-sp" style="font-size: 1.1rem; font-weight: 800">Transporte</h5>
        <table class="form-table p-2">  
            <tr>
                <th class="row-title text-sp">
                    <label for="id_modalidad_transporte">Modalidad de transporte</label>
                </th>
                <td>
                    <select class="regular-text form-control form-control-tpv" id="id_modalidad_transporte" name="mod_transporte">
                        <?php foreach($data_transporte as $key){ 
                            if($key['default']){?>
                                <option value="<?php echo $key['code'];?>">  <?php echo $key['label'];?> </option>
                        <?php
                            } else{ ?>
                                <option value="<?php echo $key['code'];?>">  <?php echo $key['label'];?> </option>
                        <?php
                            }                        
                        ?>
                            
                        <?php }?>
                    </select>  
                                   
                </td>
            </tr>
            <tr>
                <th class="row-title text-sp">
                    <label for="id_num_transporte">Número de identificación del transporte</label>
                </th>
                <td>
                    <input autocomplete="off" placeholder="N° identificación del transporte" id="id_num_transporte" name="n_transporte" class="regular-text form-control form-control-tpv" type="text" 
                    value="<?php if($config_sales){echo $config_sales->id_transport;}?>">                   
                </td>
            </tr>
            <tr>
                <th class="row-title text-sp">
                    <label for="id_name_transportista">Nombre transportista</label>
                </th>
                <td>
                    <input autocomplete="off" placeholder="Nombre del transportista" id="id_name_transportista" name="name_transportista" class="regular-text form-control form-control-tpv" type="text" 
                    value="<?php if($config_sales){echo $config_sales->nombre_transportista;}?>">                   
                </td>
            </tr>

        </table>

    </div>
    <hr class="divideer">
    <div class="row">        
        <div class="col-md-6 col-container-button">
            <button id="sendSaleForm" type="button" class="btn btn-bc btn-lg btn-block"><b>Guardar cambios</b></button>
        </div>        
    </div>
</form>