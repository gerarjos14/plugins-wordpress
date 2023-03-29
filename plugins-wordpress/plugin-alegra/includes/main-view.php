<?php
global $wpdb;
$query = "SELECT * FROM {$wpdb->prefix}wab_keys ORDER BY id ASC LIMIT 1;";
$result = $wpdb->get_row($query);
?>


<link href='https://fonts.googleapis.com/css?family=Baloo+2' rel='stylesheet'>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<link rel="stylesheet" href="<?php echo BC_PLUGIN_DIR_URL . '/public/css/bc-admin.css' ?>">


<div class="wrap">
    <div class="container">
        <div class="col-md-12">
            <div class="card card-config">
                <div class="row">
                    <a id="changeKeys" class="col-sm-4 title-action-bc text-bc btn btn-bc btn-sm ">
                        Configurar
                    </a>
                    <div class="col-sm-1"></div>
                    <a id="info" class="col-sm-4 title-action-bc text-bc btn btn-bc btn-sm ">
                        Información
                    </a>
                </div>

            </div>
        </div>

        <br><br>
        <div class="col-md-12">
            <div class="card card-pedidos">
                <table class="table  table-bordered table-bc">
                    <thead class="table-thead">
                        <tr>
                            <th class="text-bc">Token</th>
                            <th class="text-bc">Activar en: "pedido en estado..."</th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-bc">
                                <?php
                                if ($result) {
                                    echo $result->token;
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="text-bc">
                                <?php
                                if ($result) {
                                    if ($result->order_status) {
                                        echo 'Completado';
                                    } else {
                                        echo 'Procesando';
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>



    </div>
</div>
<div id="modalChangeKey" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 600px!important; margin-top:4rem!important;">
        <div class="modal-content" style="border-radius:0px!important">
            <div class="modal-header">
            <h5 class="text-bc"><b>Configurar</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" class="form-keys">
                <div class="modal-body">
                    <h5 class="text-bc"><b>Claves</b></h5>
                    <table class="form-table p-2">
                        <tr>
                            <th class="row-title">
                                <label for="token">Token</label>
                            </th>
                            <td>
                                <input id="token" name="token" class="regular-text form-control text-bc" type="text" value="<?php if ($result) {
                                                                                                                        echo $result->token;
                                                                                                                    } ?>">
                            </td>
                        </tr>
                    </table>
                    <h5 class="text-bc"><b>Otro</b></h5>
                    <table class="form-table p-2">
                        <tr>
                            <th class="row-title">
                                <label class="text-bc" for="order_status">Pedido en estado:</label>
                            </th>
                            <td>
                                <select name="order_status" id="order_status" class="text-bc regular-text form-control">
                                    <option value="0" <?php if ($result) {
                                                            if (!$result->order_status) {
                                                                echo 'selected';
                                                            }
                                                        } ?>>Procesando</option>
                                    <option value="1" <?php if ($result) {
                                                            if ($result->order_status) {
                                                                echo 'selected';
                                                            }
                                                        } ?>>Completado</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button id="sendForm" type="button" class="btn btn-bc text-bc">Guardar cambios</button>
                    <button type="button" class="btn btn-secondary-bc text-bc"  data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modalInfo" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 600px!important; margin-top:4rem!important;">
        <div class="modal-content" style="border-radius:0px!important">
            <div class="modal-header">
                <h5 class="text-bc"><b>Información</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-bc">
                    Recuerde que el cliente debe completar los siguientes campos de forma obligatoria 
                    para que la facturación pueda funcionar correctamente.
                </div>

                <table class="form-table p-2">
                    <tr>
                        <th class="row-title text-bc">
                            Nombre
                        </th>
                        <td>
                            Nombre del cliente
                        </td>
                    </tr>
                    <tr>
                        <th class="row-title text-bc">
                            Apellido
                        </th>
                        <td>
                            Apellido del cliente
                        </td>
                    </tr>
                    <tr>
                        <th class="row-title text-bc">
                            Email
                        </th>
                        <td>
                            Email del cliente
                        </td>
                    </tr>
                    <tr>
                        <th class="row-title text-bc">
                            Telefono
                        </th>
                        <td>
                            Telefono del cliente
                        </td>
                    </tr>
                    <tr>
                        <th class="row-title text-bc">
                            Dirección
                        </th>
                        <td>
                            Dirección del cliente
                        </td>
                    </tr>
                    <tr>
                        <th class="row-title text-bc">
                            Ciudad
                        </th>
                        <td>
                            Ciudad del cliente
                        </td>
                    </tr>
                    <tr>
                        <th class="row-title text-bc">
                            Codigo postal
                        </th>
                        <td>
                            Codigo postal del cliente
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-bc text-bc" data-dismiss="modal">Comprendido</button>
            </div>
        </div>
    </div>
</div>