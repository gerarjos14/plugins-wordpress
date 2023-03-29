@extends('adminlte::page')

@section('content')
@extends('adminlte::page')

@section('content')
@include('components.alert')

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Configurar claves</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.user-key.update', $user_key->id)}}"  method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="website">Sitio Web</label>
                            <input type="text" name="website" id="website"
                                class="form-control @error('website') is-invalid @enderror"
                                value={{ $user_key->website }}
                            >
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="alegra_user">Alegra User</label>
                            <input type="text" name="alegra_user" id="alegra_user"
                                class="form-control @error('alegra_user') is-invalid @enderror"
                                value={{ $user_key->alegra_user }}
                            >
                            @error('alegra_user')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="alegra_token">Alegra Token</label>
                            <input type="text" name="alegra_token" id="alegra_token"
                                class="form-control @error('alegra_token') is-invalid @enderror"
                                value={{ $user_key->alegra_token }}
                            >
                            @error('alegra_token')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="wc_consumer_key">Woocommerce Consumer Key</label>
                            <input type="text" name="wc_consumer_key" id="wc_consumer_key"
                                class="form-control @error('wc_consumer_key') is-invalid @enderror"
                                value={{ $user_key->wc_consumer_key }}
                            >
                            @error('wc_consumer_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="wc_consumer_secret">Woocommerce Consumer Secret</label>
                            <input type="text" name="wc_consumer_secret" id="wc_consumer_secret"
                                class="form-control @error('wc_consumer_secret') is-invalid @enderror"
                                value={{ $user_key->wc_consumer_secret }}
                            >
                            @error('wc_consumer_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block shadow-sm">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection