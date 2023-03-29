@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.jConfirm', true)

@section('content')

@include('components.alert')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center ">
                    <h3 class="card-title mr-2">Listado de pagos (suscripciones)</h3>
                </div>
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Precio de venta</th>
                                <th>Precio base</th>
                                <th>Stripe (charge_id)</th>
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
                ajax: "{{route('admin.payments.datatable')}}",
                columns: [
                    {data: 'user'},
                    {data: 'sale_price'},
                    {data: 'base_price'},
                    {data: 'charge_id'},
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
                }
            });
        });
    </script>
@endsection