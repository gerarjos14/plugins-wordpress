<?php

 /**
  * Proporcionar una vista de área de administración para el plugin
  *
  * Este archivo se utiliza para marcar los aspectos de administración del plugin.
  *
  * @since desde 1.0.0
  *
  * @package    Siigo_Connector
  * @subpackage Siigo_Connector/admin/partials
  */
  $result =  $this->helpers->get_keys_db();  
?>
<div class="wrap">
  <a id="changeKeys" class="page-title-action" style="font-size: 15px;">Configurar</a>
  <div id="modalChangeKey" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 600px!important; margin-top:4rem!important;">
      <div class="modal-content" style="border-radius:0px!important">
        <div class="precargador">
          <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
        <div class="modal-header">
          <h5>Configurar</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" class="form-keys">
          <div class="modal-body">
            <h5 style="font-size: 1.1rem;">Sitio Web</h5>
            <table class="form-table p-2">   
              <tr>
                  <th class="row-title">
                      <label for="website">Url</label>
                  </th>
                  <td>
                      <input id="website" name="website" class="regular-text form-control" type="text" 
                      value="<?php if($result){echo $result->website;}?>"
                      >
                  </td>
              </tr>   
            </table>   
            <hr>
            <h5 style="font-size: 1.1rem;">Api Siigo</h5>
            <table class="form-table p-2">  
                <tr>
                    <th class="row-title">
                        <label for="username">Nombre de usuario</label>
                    </th>
                    <td>
                        <input id="username" name="username" class="regular-text form-control" type="text" 
                        value="<?php if($result){echo $result->username;}?>"
                        >
                    </td>
                </tr>      
                <tr>
                    <th class="row-title">
                        <label for="access_key">Clave del acceso</label>
                    </th>      
                    <td>
                        <input id="access_key" name="access_key" class="regular-text form-control" type="text" 
                        value="<?php if($result){echo $result->access_key;}?>"
                        >
                    </td>             
                </tr>   
            </table>   
            <hr> 
            <h5 style="font-size: 1.1rem;">Woocommerce</h5>
            <table class="form-table p-2">   
              <tr>
                  <th class="row-title">
                      <label for="consumer_key">Clave del cliente</label>
                  </th>      
                  <td>
                      <input id="consumer_key" name="consumer_key" class="regular-text form-control" type="text" 
                      value="<?php if($result){echo $result->consumer_key;}?>"
                      >
                  </td>             
              </tr>          
              <tr>
                  <th class="row-title">
                      <label for="consumer_secret">Clave secreta de cliente</label>
                  </th>      
                  <td>
                      <input id="consumer_secret" name="consumer_secret" class="regular-text form-control" type="text" 
                      value="<?php if($result){echo $result->consumer_secret;}?>"
                      >
                  </td>             
              </tr>       
            </table>  
          </div>
          <div class="modal-footer">
            <button id="send-keys" type="button" class="btn btn-primary">Guardar cambios</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
