@extends('adminlte::page')
@include('components.logo')
@section('content')
@include('components.alert')
<div class="container">

    <div class="row">
        <div class="col-md-8 offset-md-2 ">
            <div class="card mb-2">
                <div class="card-header d-flex align-items-center ">
                    <h3 class="card-title">Compañia</h3>
                    @isset($company)
                        <a href="{{route('customer.signatures.create')}}" class="btn btn-sm btn-outline-success ml-2"><i class="fas fa-plus mr-1"></i></i>Ingresar firma</a>
                        <a href="{{route('customer.cafs.create')}}" class="btn btn-sm btn-outline-success ml-2"><i class="fas fa-plus mr-1"></i></i>Ingresar CAF</a>
                    @endisset
                </div>
                <div class="card-body">
                    <form action="{{route('customer.companies.store')}}" enctype="multipart/form-data" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <div class="custom-file">
                                <input type="file" @error('logo') is-invalid @enderror class="custom-file-input" id="logo" name="logo">
                                <label class="custom-file-label" for="logo">Elegir Logo</label>
                              </div>

                                 @error('logo')
                              <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                        </div>
                        @if (isset($company->logo))

                        <img  width="150"  class="rounded mx-auto d-block" src="{{url(App\Models\Company::PATH_LOGO.$company->logo)}}">

                        @endif
                        <div class="form-group">
                            <label for="rut">Rut</label>
                            <input type="text" class="form-control @error('rut') is-invalid @enderror" id="rut" name="rut"
                                value="{{ isset($company) ? $company->rut :  old('rut') }}"
                            >
                            @error('rut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ isset($company) ? $company->name :  old('name') }}"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Dirección</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address"
                                value="{{ isset($company) ? $company->address :  old('address') }}"
                            >
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="state_id">Comuna</label>
                            <select name="state_id" id="state_id" class="form-control @error('state_id') is-invalid @enderror">
                                @foreach ($states as $state)
                                    <option value="{{$state->id}}"
                                        @if(isset($company) && $company->state_id == $state->id)
                                        selected="selected"
                                        @endif
                                    >{{$state->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                value="{{ isset($company) ? $company->email :  old('email') }}"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Teléfono</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                value="{{ isset($company) ? $company->phone :  old('phone') }}"
                            >
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ecomerce_url">Url Ecommerce (Wordpress)</label>
                            <input type="text" class="form-control @error('ecomerce_url') is-invalid @enderror" id="ecomerce_url" name="ecomerce_url"
                                value="{{ isset($company) ? $company->ecomerce_url :  old('ecomerce_url') }}"
                            >
                            @error('ecomerce_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="resolution_nro">Num de resolución</label>
                            <input type="number" class="form-control @error('resolution_nro') is-invalid @enderror" id="resolution_nro" name="resolution_nro"
                                value="{{ isset($company) ? $company->resolution_nro :  old('resolution_nro') }}"
                            >
                            @error('resolution_nro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="resolution_date">Fecha de resolución</label>
                            <input type="date" class="form-control @error('resolution_date') is-invalid @enderror" id="resolution_date" name="resolution_date"
                                value="{{ isset($company) ? $company->resolution_date :  old('resolution_date') }}"
                            >
                            @error('resolution_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gr">Giro</label>
                            <input type="text" class="form-control @error('gr') is-invalid @enderror" id="gr" name="gr"
                                value="{{ isset($company) ? $company->gr :  old('gr') }}"
                            >
                            @error('gr')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="economy_activity">Número de actividad economica</label>
                            <input type="number" class="form-control @error('economy_activity') is-invalid @enderror" id="economy_activity" name="economy_activity"
                                value="{{ isset($company) ? $company->economy_activity :  old('economy_activity') }}"
                            >
                            @error('economy_activity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input
                                @if (isset($company) && $company->type_document=='invoice') checked @endif class="form-check-input @error('type_document') is-invalid @enderror"  type="radio" name="type_document" id="invoice" value="invoice">
                                <label class="form-check-label" for="invoice">
                                    Factura electrónica
                                </label>
                            </div>
                            <div class="form-check">
                              <input
                              @if (isset($company) && $company->type_document=='exempt_invoice') checked @endif
                              class="form-check-input @error('type_document') is-invalid @enderror" type="radio" name="type_document" id="exempt_invoice" value="exempt_invoice">
                              <label class="form-check-label" for="invoice">
                                Factura Exenta
                              </label>
                            </div>
                            <div class="form-check">
                                <input
                                @if (isset($company) && $company->type_document=='ballot') checked @endif
                                class="form-check-input @error('type_document') is-invalid @enderror" type="radio" name="type_document" id="ballot" value="ballot">
                                <label class="form-check-label" for="ballot">
                                    Boleta electrónica
                                </label>
                            </div>
                            @error('type_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input
                                @if (isset($company) && $company->type_document=='invoice') checked @endif class="form-check-input @error('type_document') is-invalid @enderror"  type="radio" name="is_wordpress" value="1">
                                <label class="form-check-label" for="invoice">
                                    Wordpress
                                </label>
                            </div>
                            <div class="form-check">
                              <input
                              @if (isset($company) && $company->type_document=='exempt_invoice') checked @endif
                              class="form-check-input @error('type_document') is-invalid @enderror" type="radio" name="is_wordpress" value="0">
                              <label class="form-check-label" for="invoice">
                                Ecommerce
                              </label>
                            </div>                            
                            @error('type_document')
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
