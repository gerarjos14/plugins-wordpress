@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.jConfirm', true)

@section('content')

@include('components.alert')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">                
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Titular de la cuenta</th>
                                <th>Cedula</th> 
                                <th>Número de cuenta</th>
                                <th>Tipo de cuenta</th>
                                <th>Nombre del banco</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script>
        jQuery(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true,
                autoWidth: false,
                ajax: "{{route('admin.bank-account.datatable')}}",
                columns: [
                    {data: 'user'},
                    {data: 'name'},
                    {data: 'identity_card'},
                    {data: 'account_number'},
                    {data: 'account_type'},
                    {data: 'bank_name'},
                    {data: 'action', width: 60},
                ],
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por pagina",
                    "zeroRecords": "No se encontró nada, lo siento",
                    "info": "Mostrando página _PAGE_ de _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(Filtrado de _MAX_ registros totales)",
                    "search": "Buscar",
                    "paginate": {
                        "next": "Siguiente",
                        "previous": "Anterior",
                    }
                },
            });
        });
    </script>
@endsection