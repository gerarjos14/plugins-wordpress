@extends('adminlte::page')
@include('components.logo')

@section('content')
<div class="row">
    <div class="col-lg-4 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
          <div class="inner">
            <h3>{{auth()->user()->customers()->count()}}</h3>
            <p>CLIENTES REGISTRADOS</p>
          </div>
          <div class="icon pr-3">
            <i class="fas fa-user-plus"></i>
          </div>
          <a href="{{route('agency.customers.index')}}" class="small-box-footer py-2 text-uppercase">
            Listado de clientes <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    {{-- 
    <div class="col-lg-4 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
          <div class="inner">
            <h3>44</h3>
            <p>SUSCRIPCIONES</p>
          </div>
          <div class="icon pr-3">
            <i class="fas fa-handshake"></i>
          </div>
          <a href="{{route('agency.plans.index')}}" class="small-box-footer py-2 text-uppercase">
            Listado de planes <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div> --}}
    <div class="col-lg-4 col-6">
        <!-- small card -->
        <div class="small-box bg-info">
            <div class="inner pl-3">
                <h3>{{auth()->user()->balance}}</h3>
                <p>SALDO</p>
            </div>
            <div class="icon pr-3">
                <i class="fas fa-wallet"></i>
            </div>
            <a href="{{route('agency.transfer-request.create')}}" class="small-box-footer py-2 text-uppercase">
                Solicitud de retiro de fondos <i class="ml-1 fas fa-exchange-alt"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-6"></div>
    <div class="col-lg-4 col-6">
        <div class="card">
            
            <div class="card-body">
                <h5 class="text-center">Logo de la agencia</h5>
                @if (auth()->user()->image)
                    <img class="m-auto d-block" src="{{asset(auth()->user()->image)}}">                    
                @else
                    <p>Sube una imagen...</p>
                @endif
                <form class="pt-2" action="{{route('agency.store-logo')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">Imagen</label>                        
                        <input type="file" class="form-control-file @error('image') is-invalid @enderror" name="image" id="image">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror                       
                    </div>
                    <div class="form-group">
                        <p class="m-0">La imagen se adaptara a:</p>
                        <li class="m-0">Ancho maximo de 234px.</li>
                        <li class="m-0">Alto maximo de 40px.</li>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
          </div>
    </div>
</div>

@endsection
