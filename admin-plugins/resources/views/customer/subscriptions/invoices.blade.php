@extends('adminlte::page')
@include('components.logo')
@section('content_header')
    <h1>Facturación</h1>
@endsection

@section('content')    
    @include('components.alert')
    
    @if (!auth()->user()->hasPaymentMethod())
        <div class="m-3 alert alert-danger text-center">
            <span class="fas fa-exclamation-circle"></span> {{ __("Todavía no has vinculado ninguna tarjeta a tu cuenta") }} <a href="{{ route('customer.billing.credit_card_form') }}">{{ __("Házlo ahora") }}</a>
        </div>
    @else
        <div class="row">       
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Fecha de la suscripción</th>
                                        <th>Coste de la suscripción</th>
                                        <th>Descargar factura</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse (auth()->user()->invoices() as $invoice)
                                        <tr>
                                            <td>{{ $invoice->date()->format('d/m/Y') }}</td>
                                            <td>{{ $invoice->total() }}</td>
                                            <td><a href="{{route('customer.billing.downloadInvoice',$invoice->id)}}">Descargar</a></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">Actualmente no tienes facturas para listar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @include('customer.subscriptions.partials.table')  
        </div>  
    @endif
    
@endsection

