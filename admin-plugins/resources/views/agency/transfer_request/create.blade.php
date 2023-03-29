@extends('adminlte::page')
@include('components.logo')
@section('content')
@include('components.alert')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 ">
                <div class="card mb-2">
                    <div class="card-header">
                        <h3 class="card-title">Solicitud de tranferencia de fondos</h3>
                    </div>                   
                    <div class="card-body">
                        @if (auth()->user()->balance > 0)
                            <form action="{{route('agency.transfer-request.store')}}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="amount">Indica el monto que deseas</label>
                                    <input type="number" name="amount" id="amount"
                                        class="form-control @error('amount') is-invalid @enderror"
                                    >
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <p>
                                    <i>El retiro tiene un costo del 4% y una demora de 8 dias.</i>
                                </p>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary shadow-sm">Guardar</button>
                                </div>
                            </form>
                        @else
                            Actualmente no tienes fondos.
                        @endif                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection