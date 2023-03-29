@extends('adminlte::page')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Crear Licencia</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.licenses.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="api_platform">Plataforma</label>
                            <select name="api_platform" id="api_platform" class="form-control @error('api_platform') is-invalid @enderror">
                                <option value="{{App\License::SIIGO}}">{{App\License::SIIGO}}</option>
                                <option value="{{App\License::ALEGRA}}">{{App\License::ALEGRA}}</option>
                            </select>
                            @error('api_platform')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="website">Sitio web</label>
                            <input type="text" class="form-control @error('website') is-invalid @enderror" id="website" name="website">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="ends_at">Vencimiento</label>
                            <input type="date" class="form-control @error('ends_at') is-invalid @enderror" id="ends_at" name="ends_at">
                            @error('ends_at')
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