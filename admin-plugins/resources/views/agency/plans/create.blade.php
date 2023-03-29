@extends('adminlte::page')
@include('components.logo')
@push("css")
    <style>
        li{
            color: crimson;
        }
    </style>
@endpush

@section('content')
@include('components.alert')

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Crear plan</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route("agency.plans.store") }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="platform">Plataforma</label>
                            <select name="platform" id="platform" class="form-control @error('platform') is-invalid @enderror">
                                @foreach ($platforms as $platform )
                                    <option value="{{$platform}}">{{$platform}}</option>
                                @endforeach
                            </select>
                            @error('platform')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="plan_name">Nombre del plan</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    name="plan_name"
                                    placeholder="Nombre del plan"
                                    class="form-control @error('plan_name') is-invalid @enderror"
                                    value="{{
                                        old('plan_name') ? old('plan_name') : ""
                                    }}"
                                />
                                @error('plan_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="interval">Intervalo</label>
                            <select name="interval" id="interval" class="form-control @error('interval') is-invalid @enderror">
                                @if (!$isChile)
                                    <option value="{{App\Models\Plan::MONTH}}">Mensual</option>
                                @endif
                                <option value="{{App\Models\Plan::YEAR}}">Anual</option>
                            </select>
                            @error('interval')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea name="description" name="description" 
                            class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Ingrese aquí la descripción"></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <hr class="my-4">
                        <div class="form-group">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <td colspan="3" class="text-center">Tabla de precios</td>
                                    </tr>
                                    <tr>
                                        <td>Plataforma</td>
                                        <td>Periodo</td>
                                        <td>Precio base</td>
                                    </tr>
                                </thead>    
                                <tbody>
                                    @foreach ($plans as $plan )
                                        <tr>
                                            <td>{{$plan->platform}}</td>
                                            <td>{{$plan->period}}</td>
                                            <td>{{$plan->amount}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>                     
                            </table>
                            <label for="plan_price">Precio del plan</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    name="plan_price"
                                    placeholder="Precio del plan en dolares"
                                    class="form-control @error('plan_price') is-invalid @enderror"
                                    value="{{
                                        old('plan_price') ? old('plan_price') : ""
                                    }}"
                                />
                                @error('plan_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>     
                        @if ($isChile)
                            <div class="form-group">
                                <label for="qty_documents">Cantidad de documentos</label>
                                <div class="input-group">
                                    <input
                                        type="number"
                                        name="qty_documents"
                                        placeholder="Cantidad de documentos"
                                        class="form-control @error('qty_documents') is-invalid @enderror"
                                        value="{{$plan->qty_documents}}"
                                    />
                                    @error('qty_documents')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> 
                        @endif                   
                        <button type="submit" class="btn btn-primary btn-block shadow-sm">Crear nuevo plan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection