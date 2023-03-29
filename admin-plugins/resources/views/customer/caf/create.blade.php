@extends('adminlte::page')
@include('components.logo')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-header bg-blue">
                        <h3 class="card-title">CAF</h3>
                    </div>
                    <div class="card-body ">
                        <form action="{{ route('customer.cafs.store') }}" enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="logo">Folio</label>
                                    <div class="custom-file">
                                        <input type="file" form-control-file @error('folio') is-invalid @enderror id="folio"
                                            name="folio">
                                        <label class="custom-file-label" for="folio">Seleccione Folio</label>
                                    </div>

                                    @error('folio')
                                        <p class="alert alert-danger mt-3">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                        @if (Session::has('error'))
                            <p class="alert alert-{{ Session::get('error')[0] }}">{{ Session::get('error')[1] }}</p>
                        @endif
                        <strong>Lista de Folios Disponibles</strong>
                        @include('components.listCaf')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
