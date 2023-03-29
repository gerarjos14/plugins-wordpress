@extends('adminlte::page')
@section('content')
@include('components.alert')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 ">
                <div class="card mb-2">
                    <div class="card-header">
                        <h3 class="card-title">Cuenta bancaria</h3>
                    </div>                   
                    <div class="card-body">
                        <form action="{{route('admin.bank-account.update', $bank_account->id)}}"  method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ $bank_account->name }}"
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="last_name">Apellido</label>
                                <input type="text" name="last_name" id="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ $bank_account->last_name }}"
                                >
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="account_number">Número de cuenta</label>
                                <input type="text" name="account_number" id="account_number"
                                    class="form-control @error('account_number') is-invalid @enderror"
                                    value="{{ $bank_account->account_number }}"
                                >
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="account_type">Tipo de cuenta</label>
                                <select name="account_type" id="account_type" class="form-control @error('account_type') is-invalid @enderror">
                                    <option value="{{App\Models\BankAccount::SAVINGS}}" {{$bank_account->account_type == App\Models\BankAccount::SAVINGS ? 'selected' : '' }}>Cuenta de ahorro</option>
                                    <option value="{{App\Models\BankAccount::CHECKING}}" {{$bank_account->account_type == App\Models\BankAccount::CHECKING ? 'selected' : '' }}>Cuenta corriente</option>
                                </select>
                                @error('account_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="bank_name">Nombre del banco</label>
                                <input type="text" name="bank_name" id="bank_name"
                                    class="form-control @error('bank_name') is-invalid @enderror"
                                    value="{{ $bank_account->bank_name }}"
                                >
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="identity_card">Cedula</label>
                                <input type="text" name="identity_card" id="identity_card"
                                    class="form-control @error('identity_card') is-invalid @enderror"
                                    value="{{ $bank_account->identity_card }}"
                                >
                                @error('identity_card')
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