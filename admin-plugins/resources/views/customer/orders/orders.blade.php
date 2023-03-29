@extends('adminlte::page')
@include('components.logo')
@section('content')
    @include('components.alert')
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-header d-flex align-items-center ">
                        <h3 class="card-title">Ordenes</h3>
                        @isset($company)
                            <a href="{{ route('customer.signatures.create') }}" class="btn btn-sm btn-outline-success ml-2"><i
                                    class="fas fa-plus mr-1"></i></i>Ingresar firma</a>
                            <a href="{{ route('customer.cafs.create') }}" class="btn btn-sm btn-outline-success ml-2"><i
                                    class="fas fa-plus mr-1"></i></i>Ingresar CAF</a>
                        @endisset
                    </div>
                    <div class="card-body">



                        <table id="table" class="table">
                            <thead>
                                <th>Cliente</th>
                                <th>Rut</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $order->name }}</td>
                                        <td>{{ $order->rut }}</td>
                                        <td>{{ $order->created_at }}</td>
                                        <td>{!! $order->dtes_id != null ? '<span class="badge badge-success">DTE Creado</span>' : '<span class="badge badge-danger">Error al enviar</span>' !!}</td>
                                        <td><a href="{{ route('customer.order.show', $order->order_id) }}"
                                                class="btn btn-primary" style="color: white">Ver Detalles</a></td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        $('#table').DataTable({
        language: {
            paginate: {
                previous: '<i class="fas fa-angle-left"></i>',
                next: '<i class="fas fa-angle-right"></i>'
            }
        },
        processing: true,
        serverSide: true,
        order: [
            [3, "desc"]
        ],
        });
        });
    </script>
@endsection
