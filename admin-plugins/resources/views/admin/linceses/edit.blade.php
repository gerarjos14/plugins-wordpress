@extends('adminlte::page')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Editar licencia</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.licenses.update', $license->id)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" id="email" name="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{$license->email}}"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="website">Sitio web</label>
                            <input type="text" id="website" name="website"
                                class="form-control @error('website') is-invalid @enderror" 
                                value="{{$license->website}}"
                            >
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="ends_at">Vencimiento</label>
                            <input type="date" id="ends_at" name="ends_at" 
                                class="form-control @error('ends_at') is-invalid @enderror"                             
                                value="{{\Carbon\Carbon::parse($license->ends_at)->format('Y-m-d')}}"
                            >
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
