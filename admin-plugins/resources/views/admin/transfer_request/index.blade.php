@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.jConfirm', true)

@push('css')
    <style>
        .table-agency{
            width: 100%;
        }
        .table-agency tr td:first-child{
            font-weight: 700;
        }
        .table-agency td{
            width: 35%;
            padding: .75rem!important;
        }
    </style>
@endpush

@section('content')

@include('components.alert')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center ">
                    <h3 class="card-title mr-2">Listado de solicitudes</h3>
                </div>
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                
                                <th>Agencia</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Fecha de creación</th>
                                <th>Fecha de "acuso de recibo"</th>
                                <th>Fecha de confirmación</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modal">
</div>

@endsection
@section('js')
    <script>
        jQuery(document).ready(function() {
            $.jConfirm.defaults.question = '¿Estás seguro?';
            $.jConfirm.defaults.confirm_text = 'Sí';
            $.jConfirm.defaults.deny_text = 'No';
            $.jConfirm.defaults.position = 'top';
            $.jConfirm.defaults.theme = 'black';            
            $('#myTable').DataTable({
                responsive: true,
                autoWidth: false,
                ajax: "{{route('admin.transfer-request.datatable')}}",
                columns: [
                    {data: 'user'},
                    {data: 'amount'},
                    {data: 'status'},
                    {data: 'created_at'},
                    {data: 'pending_at'},
                    {data: 'confirmed_at'},
                    {data: 'action', width: '80'},
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
                "fnDrawCallback": function( oSettings ) {

                    $('.ajax-modal').click(function() {
                        const btn = $(this);
                        const route = btn.data("route");
                        jQuery.ajax({
                            method: 'GET',
                            url: route,
                            success: function (data) {
                                console.log(data);
                                $('#modal').html(data);
                                $('#modal').modal('show');
                            },
                            error: function (error) {
                               console.log(error)
                            },
                        })
                    });

                    $('.transfer-status').jConfirm().on('confirm', function(e){
                        const btn = $(this);
                        const route = btn.data("route");
                        jQuery.ajax({
                            method: 'POST',
                            url: route,
                            data: { "_token": "{{ csrf_token() }}"},
                            success: function (data) {
                                window.location.reload();
                            },
                            error: function (error) {
                               console.log(error)
                            },
                        })
                    })
                },
            });
        });
    </script>
@endsection
