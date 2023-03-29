

<form method="POST" class="form-keys">
    <div class="modal-body">
        <table class="form-table p-2">
            <tr>
                <th class="row-title">
                    <label class="text-bc" for="token">Token</label>
                </th>
                <td>
                    <input id="token" name="token" class="regular-text form-control inputs-admin-config" type="text" 
                    value="<?php if ($result) {echo $result->token;} ?>">
                </td>
            </tr>            
        </table>
        <input type="hidden" id="service-nonce" name="nonce" value="<?php echo wp_create_nonce( 'bc_seg' );?>">


        
    </div>
    <div class="modal-footer">
        <button id="send-keys" type="button" class="btn btn-bc btn-submit-config text-bc">Guardar cambios</button>
    </div>
</form>
<script src="<?php echo BILL_PLUGIN_DIR_URL . 'admin/js/bc-admin.js' ?>"></script>
