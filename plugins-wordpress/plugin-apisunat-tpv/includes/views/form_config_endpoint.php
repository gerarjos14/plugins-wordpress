<form method="POST" class="form-keys">
    <div class="modal-body">
        <h5 class="text-sp" style="font-size: 1.1rem; font-weight: 800">Ingresa URL del TPV</h5>
        <table class="form-table p-2">  
            <tr>
                <th class="row-title text-sp">
                    <label for="website_tpv">Url TPV</label>
                </th>
                <td>
                    <input  id="website_tpv" name="website_tpv" class="regular-text form-control form-control-tpv" type="text" 
                    value="<?php if($result){echo $result->website_tpv;}?>"
                    >
                </td>
                <input type="hidden"  id="url_sist_change" name="url_sist_change" value="1">
            </tr>                
        </table>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <button type="button" class="btn btn-secondary-sp btn-lg btn-block " data-dismiss="modal"><b>Cancelar</b></button>
        </div>
        <div class="col-md-6">
        <button id="sendForm" type="button" class="btn btn-bc btn-lg btn-block"><b>Guardar cambios</b></button>
        </div>        
    </div>
</form>