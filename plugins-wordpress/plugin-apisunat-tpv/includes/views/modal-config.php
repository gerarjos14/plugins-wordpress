<link rel="stylesheet" href="<?php echo lars_pos_PLUGIN_DIR_URL . 'public/css/forms_design.css' ?>">


<div id="modalChangeKey" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="max-width: 600px!important; margin-top:4rem!important;">
    <div class="modal-content" style="border-radius:0px!important">
      <div class="modal-header">
        <h5 class="text-sp title-modal" style="font-weight: 800;">Configuración del plugin</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" class="form-keys">
        <div class="modal-body">
            <h5 class="text-sp" style="font-size: 1.1rem; font-weight: 800">Sitio Web</h5>
            <table class="form-table p-2">  
                <tr>
                    <th class="row-title text-sp">
                        <label for="website">Url</label>
                    </th>
                    <td>
                        <input id="website" name="website" class="regular-text form-control form-control-tpv" type="text" 
                        value="<?php if($result){echo $result->website;}?>"
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
                        <input id="consumer_key" name="consumer_key" class="regular-text form-control form-control-tpv" type="text" 
                        value="<?php if($result){echo $result->consumer_key;}?>"
                        >
                    </td>             
                </tr>
                <tr>
                    <th class="row-title text-sp">
                        <label for="consumer_secret">Clave secreta de cliente</label>
                    </th>      
                    <td>
                        <input id="consumer_secret" name="consumer_secret" class="regular-text form-control form-control-tpv" type="text" 
                        value="<?php if($result){echo $result->consumer_secret;}?>"
                        >
                    </td>             
                </tr>
            </table>
        </div>
        <div class="modal-footer">
          <!-- <button id="sendForm" type="button" class="btn btn-bc"><b>Guardar cambios</b></button> -->
          <button type="button" class="btn btn-secondary-sp" data-dismiss="modal"><b>Cancelar</b></button>
        </div>
      </form>
    </div>
  </div>
</div>