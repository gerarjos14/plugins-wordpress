@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.jConfirm', true)

@include('components.logo')

@section('content')
@include('components.alert')

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mr-2">Token de acceso</h3>
                    <a href="#" data-route="{{route('customer.access-token.store')}}" class="btn btn-sm btn-outline-success new-record"><i class="fas fa-plus mr-1"></i></i>Generar nuevo</a>
                </div>
                <div class="card-body">
                    @if ($token)
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td class="text-center">Token:</td>
                                <td >{{$token->token}}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center">                                    
                                    @if ($token->blocked)
                                        <a href="#" data-route="{{route('customer.access-token.unlock', $token->id)}}" class="btn btn-sm btn-outline-primary mr-1 update-record"><i class="fa fa-check mr-1"></i>Habilitar</a>
                                    @else
                                        <a href="#" data-route="{{route('customer.access-token.lock', $token->id)}}" class="btn btn-sm btn-outline-danger update-record"><i class="fa fa-times mr-1"></i>Bloquear</a>
                                    @endif

                                </td>
                            </tr>
                        </table>
                    @else
                        <p>Actualmente no tiene un token disponible, por favor contactese con el administrador.</p>
                    @endif
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script>
        jQuery(document).ready(function() {
            $.jConfirm.defaults.question = 'Se eliminara su token actual. <br> ¿Estás seguro?';
            $.jConfirm.defaults.confirm_text = 'Sí';
            $.jConfirm.defaults.deny_text = 'No';
            $.jConfirm.defaults.position = 'top';
            $.jConfirm.defaults.theme = 'black';
            $('.new-record').jConfirm().on('confirm', function(e){
                const btn = $(this);
                const route = btn.data("route");
                jQuery.ajax({
                    method: 'POST',
                    url: route,
                    data: { "_token": "{{ csrf_token() }}"},
                    success: function (data) {
                        window.location.reload();
                    },
                    error: function (error) {
                        console.log(error)
                    },
                })
            });
            $('.update-record').click(function(){
                const btn = $(this);
                const route = btn.data("route");
                jQuery.ajax({
                    method: 'POST',
                    url: route,
                    data: { "_token": "{{ csrf_token() }}"},
                    success: function (data) {
                        window.location.reload();
                    },
                    error: function (error) {
                        console.log(error)
                    },
                })
            });   
        });
    </script>
             
             

@endsection