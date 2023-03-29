<?php
    global $wpdb;
    // valores configuracion del plugin
    $query = "SELECT * FROM {$wpdb->prefix}lars_pos_keys ORDER BY id ASC LIMIT 1;";
    $result = $wpdb->get_row( $query );
    
?>
<form method="POST" class="form-keys">
    <div class="modal-body">
        <h5 class="text-sp" style="font-size: 1.1rem; font-weight: 800">Sitio Web</h5>
        <table class="form-table p-2">  
            <tr>
                <th class="row-title text-sp">
                    <label for="website">Url Ecommerce</label>
                </th>
                <td>
                    <input autocomplete="off" placeholder="URL del sitio de wordpress" id="website" name="website" class="regular-text form-control form-control-tpv" type="text" 
                    value="<?php if($result){echo $result->website;}?>">
                    <input type="hidden" id="url_sist_change" name="url_sist_change" value="0">
                </td>
            </tr>    
            <tr>
                <th class="row-title text-sp">
                    <label for="website_tpv">Url TPV</label>
                </th>
                <td>
                    <input autocomplete="off" placeholder="URL sistema TPV (Ejemplo: https://example.com)" id="website_tpv" name="website_tpv" class="regular-text form-control form-control-tpv" type="text" 
                    value="<?php if($result){echo $result->website_tpv;}?>"
                    >
                </td>
            </tr>            
        </table>
        <h5 class="text-sp" style="font-size: 1.1rem; font-weight: 800">WooCommerce</h5>

        <table class="form-table p-2">  
            <tr>
                <th class="row-title text-sp">
                    <label for="consumer_key">Clave del cliente</label>
                </th>      
                <td>
                    <input autocomplete="off" placeholder="Clave del cliente"   id="consumer_key" name="consumer_key" class="regular-text form-control form-control-tpv" type="text" 
                    value="<?php if($result){echo $result->consumer_key;}?>"
                    >
                </td>             
            </tr>
            <tr>
                <th class="row-title text-sp">
                    <label for="consumer_secret">Clave secreta de cliente</label>
                </th>      
                <td>
                    <input autocomplete="off" placeholder="Clave secreta"  id="consumer_secret" name="consumer_secret" class="regular-text form-control form-control-tpv" type="text" 
                    value="<?php if($result){echo $result->consumer_secret;}?>"
                    >
                </td>             
            </tr>
            
        </table>
    </div>
    <hr class="divideer">
    <div class="row">
        
        <div class="col-md-6 col-container-button">
            <button id="sendForm" type="button" class="btn btn-bc btn-lg btn-block"><b>Guardar cambios</b></button>
        </div>        
    </div>
</form>