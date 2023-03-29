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
$result =  $this->helpers->get_config_db(); 
?>

<link href='https://fonts.googleapis.com/css?family=Baloo+2' rel='stylesheet'>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<link rel="stylesheet" href= "<?php echo BC_PLUGIN_DIR_URL . 'admin/css/bc-admin.css' ?>" >




<div class="wrap">
  <div class="container">
    <div class="col-md-12">
      <div class="card card-config">
        <a id="changeKeys" class="title-action-bc text-bc">Configurar</a>
      </div>
    </div>

    <div class="col-md-12">
      <div class="card card-pedidos">
        <h3 class="text-bc title-pedidos"><b>Buscar documentos por nro de pedido</b></h3> <hr>
        <div class="card-body">            
            <div class="search-content d-flex flex-wrap">
              <input type="number" name="nro-order" id="nro-order" class="mr-2">
              <button type="button" class="btn btn-bc" id="search-order">Buscar</button>
            </div>
            <br>
            <div class="search-result"  id="search-result">

            </div>
        </div>    
      </div>
    </div>
  </div>
  

  <div id="modalChangeKey" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 600px!important; margin-top:4rem!important;">
      <div class="modal-content" style="border-radius:0px!important">
        <div class="precargador">
          <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
        <div class="modal-header">
          <h5 class="text-bc">Configurar</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" class="form-keys">
          <div class="modal-body">           
            <h5 class="text-bc" style="font-size: 1.1rem;">Api Siigo</h5>
            <table class="form-table p-2">  
                <tr>
                    <th class="row-title">
                        <label class="text-bc" for="token">Token</label>
                    </th>
                    <td>
                        <input id="token" name="token" class="regular-text form-control" type="text" 
                        value="<?php if($result){echo $result->token;}?>">
                    </td>
                </tr> 
                   
                <tr>
                    <th class="row-title">
                        <label class="text-bc" for="access_key">Realizar factura cuando la orden este en el estado de:</label>
                    </th>      
                    <td>
                        <select name="order_status" id="order_status" class="regular-text form-control">
                            <option value="0" <?php if($result){ if(!$result->order_status){ echo 'selected'; }} ?> >Procesando</option>
                            <option value="1" <?php if($result){ if($result->order_status){ echo 'selected'; }} ?> >Completado</option>
                        </select>
                    </td>             
                </tr>   
            </table>   
          </div>
          <div class="modal-footer">
            <button id="send-keys" type="button" class="btn btn-bc text-bc">Guardar cambios</button>
            <button type="button" class="btn btn-secondary-bc text-bc" data-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
