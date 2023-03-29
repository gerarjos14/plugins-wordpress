@extends('adminlte::page')
@include('components.logo')
@section('content')
    @include('components.alert')
    <div class="container">
        <div class="row">
            <div class="col-12">
                @if ($emptyKeys)
                    <div class="m-3 alert alert-danger text-center">
                        <span class="fas fa-exclamation-circle"></span> Todavía no has configuado las claves de alegra.<a href="{{ route('customer.configure-keys') }}">Házlo ahora</a>
                    </div>
                @else
                    <div class="card">
                        <div class="card-header d-flex align-items-center ">
                            <h3 class="card-title mr-2">Listado de cuentas de banco de alegra</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bankAccounts as $account)
                                        <tr>
                                            <td>{{$account->name}}</td>
                                            <td>{{$account->type}}</td>
                                            <td>{{$account->description}}</td>
                                            <td>
                                                <form action="{{route('customer.bank-account.store')}}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="platform_bank_account" value="{{ $account->id }}">
                                                    @if (isset($bankAccount) && $bankAccount->account_id == $account->id)
                                                        <button type="button" disabled  class="btn btn-sm btn-outline-primary">Actual</button>
                                                    @else
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">Establecer</button>
                                                    @endif
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> 
                @endif               
                
               
            </div>
        </div>
    </div>
@endsection