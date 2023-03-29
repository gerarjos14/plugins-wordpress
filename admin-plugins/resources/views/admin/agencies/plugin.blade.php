@extends('adminlte::page')

@section('plugins.Select2', true)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Subir plugin</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.agencies.store-plugin', $agency->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf                   
                        <div class="form-group">
                            <label for="plugin">Plugin</label>
                            <input type="file" name="plugin" id="plugin" class="form-control-file @error('plugin') is-invalid @enderror" >
                            @error('plugin')
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