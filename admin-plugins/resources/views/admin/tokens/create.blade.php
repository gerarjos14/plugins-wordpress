@extends('adminlte::page')

@section('plugins.Select2', true)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Crear cliente</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.access-token.store')}}" method="POST">
                        @csrf                        
                        <div class="form-group">
                            <label for="customer"></label>
                            <select class="select-2 form-control @error('customer') is-invalid @enderror" name="customer" id="customer">
                                @foreach ($customers as $customer)
                                    <option value="{{$customer->id}}">{{$customer->name}}</option>
                                @endforeach
                            </select>
                            @error('customer')
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
@section('js')
    <script>
        jQuery(document).ready(function() {
            $('.select-2').select2();
        });
    </script>
@endsection