@extends('adminlte::page')
@include('components.logo')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Plugin</h4>
                        <p>Metodo de uso:</p>
                        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Corrupti quis totam numquam reiciendis sunt quaerat, voluptatum minus, quos libero autem tenetur aliquam ratione? Recusandae, corrupti quidem. Tempora consequuntur impedit quis?</p>
                        <a style="margin-bottom: 10px" href="{{route('customer.plugin.download')}}" class="btn btn-primary btn-sm m-l-15"><i class="fa fa-download"></i> Descargar plugin</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection