@extends('adminlte::page')
@include('components.logo')
@section('content')
    @include('components.alert')
    <div class="container">

        <div class="row">

            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-header d-flex align-items-center ">
                        <h3 class="card-title">Ordene N° {{ $order->order_id }}</h3>
                        @isset($company)
                            <a href="{{ route('customer.signatures.create') }}" class="btn btn-sm btn-outline-success ml-2"><i
                                    class="fas fa-plus mr-1"></i></i>Ingresar firma</a>
                            <a href="{{ route('customer.cafs.create') }}" class="btn btn-sm btn-outline-success ml-2"><i
                                    class="fas fa-plus mr-1"></i></i>Ingresar CAF</a>
                        @endisset
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                        @endif
                        <div class="row d-flex flex-row-reverse">
                            @if (isset($dte->envio_dte) && $dte->envio_dte->estado != 'EPR')
                                {{-- <a class="btn btn-primary text-white">Reintentar Envio</a> --}}
                            @elseif(!isset($dte))
                                {{-- <form action="{{ route('customer.create-dte', $order) }}" method="POST">
                                    <button class="btn btn-primary text-white">Reintentar Crear DTE</button>

                                </form> --}}
                            @else
                                {{-- @empty($dte->references()->first())
                                    <a href="{{route('customer.cancel-dte',$order)}}" class="btn btn-danger text-white">Anular</a>
                                @endempty --}}
                                <a target="_blank" href="{{ url('/dte/' . $dte->uuid) }}"
                                    class="btn btn-success text-white mr-2">Ver Factura</a>
                            @endif

                        </div>



                        <hr>
                        <div class="row d-flex flex-row-reverse">

                            <p class="mb-0"><Strong>Fecha:</Strong> {{ $order->created_at->format('d-m-Y') }}</p>

                        </div>
                        <p class="mb-0"><Strong>Cliente:</Strong> {{ $order->name }}</p>
                        @if ($dte->type == 33 || $dte->type == 34)
                            @empty(!$order->rut)
                                <p class="mb-0"><Strong>RUT:</Strong> {{ $order->rut }}</p>
                            @endempty
                            @empty(!$order->classification)
                                <p class="mb-0"><Strong>Giro:</Strong> {{ $order->classification }}</p>
                            @endempty
                        @endif
                        @empty(!$order->email)
                            <p class="mb-0"><Strong>E-Mail:</Strong> {{ $order->email }}</p>
                        @endempty
                        @empty(!$order->address)
                            <p class="mb-0"><Strong>Address:</Strong> {{ $order->address }}</p>
                        @endempty
                        @empty(!$order->state)
                            <p class="mb-0"><Strong>State:</Strong> {{ $order->state }}</p>
                        @endempty
                        {{-- {{$order->details}} --}}

                        <hr>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Descuento</th>
                                    <th>Sub Total</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->details as $detail)
                                    <tr>
                                        {{-- /<td scope="row"></td> --}}
                                        <td>{{ $detail->description }}</td>
                                        <td class="aling-center">{{ $detail->quantity }}</td>
                                        <td>$ {{ $detail->unit_price }}</td>
                                        <td>$ {{ $detail->discount }}</td>
                                        <td>$ {{ ($detail->unit_price - $detail->discount) * $detail->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <div class="row d-flex justify-content-end">
                            @php
                                $total = 0;
                                foreach ($order->details as $detail) {
                                    $total = $total + ($detail->unit_price - $detail->discount) * $detail->quantity;
                                }
                                echo '<strong class="mr-2">TOTAL: </strong>$' . $total;
                            @endphp

                        </div>

                        <hr>
                        <h5>Errores</h5>
                        @isset($order->log)
                            <p class="badge badge-danger"><Strong>Error:</Strong> {{ $order->log }}</p>
                        @endisset

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
