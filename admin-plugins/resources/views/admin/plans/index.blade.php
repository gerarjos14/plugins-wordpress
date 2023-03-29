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
                    <ul>
                        <li style="color: crimson;"><p>Los planes con intervalo "lifetime" son aquellos que vera un cliente el cual tiene "Permitir Vitalicio" activo. <br>La compra de estos sera de pago unico.</p></li>
                        <li style="color: crimson;"><p>Los planes con intervalo "year" y "month" son aquellos que utilizaran las agencias como "base" para establecer sus propios planes.</p></li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex align-items-center ">
                    <h3 class="card-title mr-2">Listado de planes</h3>
                </div>
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Intervalo</th>
                                <th>Precio</th>
                                <th>Moneda</th>
                                <th>Plataforma</th>
                                <th>Accion</th>
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
            $.jConfirm.defaults.question = '{{ __("¿Estás seguro?") }}';
            $.jConfirm.defaults.confirm_text = '{{ __("Sí") }}';
            $.jConfirm.defaults.deny_text = '{{ __("No") }}';
            $.jConfirm.defaults.position = 'top';
            $.jConfirm.defaults.theme = 'black';

            $('#myTable').DataTable({
                responsive: true,
                autoWidth: false,
                ajax: "{{route('admin.plans.datatable')}}",
                columns: [
                    {data: 'interval'},
                    {data: 'amount'},
                    {data: 'currency'},
                    {data: 'platform'},
                    {data: 'action'},
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
                    $('.active-record').jConfirm().on('confirm', function(e){
                        const btn = $(this);
                        const route = btn.data("route");
                        jQuery.ajax({
                            method: 'POST',
                            url: route,
                            data: { "_token": "{{ csrf_token() }}", "_method" : 'delete' },
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
