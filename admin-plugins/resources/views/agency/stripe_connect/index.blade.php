@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.jConfirm', true)
@include('components.logo')
@section('content')

@include('components.alert')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center ">
                    <h3 class="card-title mr-2">Stripe - Cuentas conectadas</h3>
                    @if (auth()->user()->account_id)
                        <a href="#" data-route="{{route('agency.stripe-connect.enable-account')}}" class="btn btn-sm btn-outline-success enabled-account">Activar Cuenta</a>
                    @endif
                </div>
                <div class="card-body">
                    @if (auth()->user()->account_id)
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab amet at voluptate cum, corporis error maiores praesentium nihil aliquam quas ea debitis suscipit nesciunt totam sequi, a earum. Reiciendis, facilis?</p>
                        <a href="{{route('agency.stripe-connect.create-account-link')}}" class="btn btn-sm btn-outline-primary">Link</a>
                    @else
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab amet at voluptate cum, corporis error maiores praesentium nihil aliquam quas ea debitis suscipit nesciunt totam sequi, a earum. Reiciendis, facilis?</p>
                        <a href="{{route('agency.stripe-connect.create')}}" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus mr-1"></i></i>Crear</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script>
        $('.enabled-account').jConfirm().on('confirm', function(e){
            const btn = $(this);
            const route = btn.data("route");
            jQuery.ajax({
                method: 'POST',
                url: route,
                data: { "_token": "{{ csrf_token() }}" },
                success: function (data) {
                    window.location.reload();
                },
                error: function (error) {
                    window.location.reload();
                },
            })
        })
    </script>
@endsection