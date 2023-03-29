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
                    <form action="{{route('customer.col-companies.store')}}" enctype="multipart/form-data" method="POST">
                        @csrf                       
                        <input type="hidden" class="form-control" id="rut" name="rut" value="2.543.825-6">
                            

                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ isset($company) ? $company->name :  old('name') }}"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                            <input type="hidden" id="address" name="address" value="Demo 1">                            
                            <input type="hidden" name="state_id" value="1">
                           
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
                       
                            <input type="hidden" class="form-control @error('resolution_nro') is-invalid @enderror" id="resolution_nro" name="resolution_nro" value="1">                            

                        
                            <input type="hidden" class="form-control @error('resolution_date') is-invalid @enderror" id="resolution_date" name="resolution_date" value="2022-01-07">
                            
                        
                            <input type="hidden" class="form-control @error('gr') is-invalid @enderror" id="gr" name="gr"
                                value="-----"
                            >

                        
                            <input type="hidden" class="form-control @error('economy_activity') is-invalid @enderror" id="economy_activity" name="economy_activity"
                                value="702001"
                            >

                            <input
                            @if (isset($company) && $company->type_document=='invoice') checked @endif class="form-check-input @error('type_document') is-invalid @enderror"  type="hidden" name="type_document" id="invoice" value="invoice">
                             
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
