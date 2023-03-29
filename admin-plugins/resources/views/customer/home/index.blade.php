@extends('adminlte::page')
@include('components.logo')
@section('content')
    <div class="container">

        <div class="row card p-3 d-flex justify-content-center">
            <div class="row">
                <div class="col-md-9 col-12 justify-content-center">
                    <h5>Lista de Folios Disponibles</h5>
                </div>
                {{-- <div class="col-md-3 col-12 right "> <a href="{{ route('customer.cafs.create') }}"
                        class="btn btn-sm btn-outline-success ml-2"><i class="fas fa-plus mr-1"></i></i>Ingresar CAF</a>
                </div> --}}
            </div>

            @include('components.listCaf')
        </div>
    </div>
@endsection
