@extends('adminlte::page')
@include('components.logo')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Firma</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('customer.signatures.store')}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="signature">Archivo</label>
                            <input type="file" name="signature" id="signature" class="form-control-file @error('signature') is-invalid @enderror" >
                            @error('signature')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ old('name') }}"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>                       --}}
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                    @if(Session::has('error'))
                    <p class="alert alert-{{Session::get('error')[0]}}">{{ Session::get('error')[1] }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
