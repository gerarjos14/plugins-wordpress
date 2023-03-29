@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.jConfirm', true)
@include('components.logo')

@section('content')

@include('components.alert')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center ">
                    <h3 class="card-title mr-2">Listado de clientes</h3>
                    <a href="{{route('agency.customers.create')}}" class="btn btn-sm btn-outline-success"><i class="fas fa-plus mr-1"></i></i>Crear</a>
                </div>
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
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
                ajax: "{{route('agency.customers.datatable')}}",
                columns: [
                    {data: 'name'},
                    {data: 'email'},
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
                    $('.delete-record').jConfirm().on('confirm', function(e){
                        const btn = $(this);
                        const route = btn.data("route");
                        jQuery.ajax({
                            method: 'DELETE',
                            url: route,
                            data: { "_token": "{{ csrf_token() }}" },
                            success: function (data) {
                                window.location.reload();
                            },
                            error: function (error) {
                                window.location.reload();
                            },
                        })
                    })
                },
            });
        });
    </script>
@endpush