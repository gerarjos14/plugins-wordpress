@extends('adminlte::page')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Editar plan</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST">
                        @csrf        
                        @method('PUT')    
                        @if ($plan->interval === \App\Models\Plan::LIFETIME)
                            <div class="form-group">
                                <label for="plan_name">Nombre del plan</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        name="plan_name"
                                        placeholder="Nombre del plan"
                                        class="form-control @error('plan_name') is-invalid @enderror"
                                        value="{{$plan->name}}"
                                    />
                                    @error('plan_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea name="description" name="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror" 
                                    placeholder="Ingrese aquí la descripción">{{$plan->description}}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>                            
                        @endif
                        <p>Precio base para los planes de la plataforma de {{$plan->platform}} con periodo {{$plan->interval}}.</p>             
                        <div class="form-group">
                            <label for="plan_price">Precio del plan</label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    name="plan_price"
                                    placeholder="Precio del plan en dolares"
                                    class="form-control @error('plan_price') is-invalid @enderror"
                                    value="{{$plan->amount}}"
                                />
                                @error('plan_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @if ($plan->platform === \App\Models\Plan::FAC_CHILE)
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
                        <button type="submit" class="btn btn-primary btn-block shadow-sm">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection