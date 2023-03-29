@extends('adminlte::page')
@include('components.logo')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Compañia</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('customer.companies.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="rut">Rut</label>
                            <input type="text" class="form-control @error('rut') is-invalid @enderror" id="rut" name="rut"
                                value="{{ old('rut') }}"
                            >
                            @error('rut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> 
                        
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ old('name') }}"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>      

                        <div class="form-group">
                            <label for="address">Dirección</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address"
                                value="{{ old('address') }}"
                            >
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>   

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                value="{{ old('email') }}"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> 

                        <div class="form-group">
                            <label for="phone">Teléfono</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                value="{{ old('phone') }}"
                            >
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> 

                        <div class="form-group">
                            <label for="resolution_nro">resolution_nro</label>
                            <input type="text" class="form-control @error('resolution_nro') is-invalid @enderror" id="resolution_nro" name="resolution_nro"
                                value="{{ old('resolution_nro') }}"
                            >
                            @error('resolution_nro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> 

                        <div class="form-group">
                            <label for="resolution_date">resolution_date</label>
                            <input type="text" class="form-control @error('resolution_date') is-invalid @enderror" id="resolution_date" name="resolution_date"
                                value="{{ old('resolution_date') }}"
                            >
                            @error('resolution_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> 

                        <div class="form-group">
                            <label for="gr">Giro</label>
                            <input type="text" class="form-control @error('gr') is-invalid @enderror" id="gr" name="gr"
                                value="{{ old('gr') }}"
                            >
                            @error('gr')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> 

                        <div class="form-group">
                            <label for="economy_activity">Actividad economica</label>
                            <input type="text" class="form-control @error('economy_activity') is-invalid @enderror" id="economy_activity" name="economy_activity"
                                value="{{ old('economy_activity') }}"
                            >
                            @error('economy_activity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>                                 
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection