@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('content')
@include('components.alert')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">  
                <div class="card-header d-flex align-items-center ">
                    <h3 class="card-title mr-2">Listado de Paises</h3>
                    <a href="{{route('admin.countries.create')}}" class="btn btn-sm btn-outline-success"><i class="fas fa-plus mr-1"></i></i>Crear</a>
                </div>              
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Código</th> 
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
                ajax: "{{route('admin.countries.datatable')}}",
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'code'},
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