@extends('adminlte::page')
@include('components.logo')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Editar plan</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.plans.update', $plan->id) }}" method="POST">
                        @csrf        
                        @method('PUT')                
                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea name="description" name="description" class="form-control" rows="3" placeholder="Ingrese aquí la descripción">{{$plan->description}}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block shadow-sm">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection