@extends('adminlte::page')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Editar cliente</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.customers.update', $customer)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ $customer->name }}"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> 
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                value="{{ $customer->email }}"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>                        
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <small style="color: crimson">Dejar en blanco si no se desea cambiar</small>
                            <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> 
                        <div class="form-group">
                            <label for="agency">Agencia (o superadmin)</label>
                            <select class="select-2 form-control @error('agency') is-invalid @enderror" name="agency" id="agency">
                                <option value="{{auth()->user()->id}}" {{auth()->user()->id == $customer->parent_id ? 'selected' : '' }}>{{auth()->user()->name}}</option>
                                @foreach ($agencies as $agency)
                                    <option value="{{$agency->id}}" {{$agency->id == $customer->parent_id ? 'selected' : '' }}>{{$agency->name}}</option>
                                @endforeach
                            </select>
                            @error('agency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>    
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input @error('allow_lifetime') is-invalid @enderror" 
                                    name="allow_lifetime" type="checkbox" id="allow_lifetime"
                                    {{$customer->allow_lifetime ? 'checked' : ''}}
                                >
                                <label for="allow_lifetime" class="custom-control-label">Permitir "Vitalicio"</label>
                                @error('allow_lifetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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