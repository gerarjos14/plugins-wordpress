
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Cuenta bancaria - {{$agency->name}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table-agency">
                <tr>
                    <td>Nombre</td>
                    <td>{{$bankAccount->name}}</td>
                </tr>
                <tr>
                    <td>Apellido</td>
                    <td>{{$bankAccount->last_name}}</td>
                </tr>
                <tr>
                    <td>Número de cuenta</td>
                    <td>{{$bankAccount->account_number}}</td>
                </tr>
                <tr>
                    <td>Tipo de cuenta</td>
                    <td>{{$bankAccount->type}}</td>
                </tr>
                <tr>
                    <td>Nombre del banco</td>
                    <td>{{$bankAccount->bank_name}}</td>
                </tr>
                <tr>
                    <td>Cedula</td>
                    <td>{{$bankAccount->identity_card}}</td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>