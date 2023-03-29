@extends('layouts.front')

@section('content')
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif


    <div class="card  p-5">
        <h1>Consultar DTE</h1>
        <form method="POST" action="{{ route('search_dte') }}">
            @csrf
            <div class="form-group">
                <input type="text" index="rut" name="rut" class="form-control" id="rut" placeholder="Rut Emisor">
            </div>

            <div class="form-group">
                <select name="type" class="form-control" id="type_dte">
                    <option value="33">Factura</option>
                    <option value="39">Boleta</option>
                    <option value="34">Factura Excenta</option>
                    <option value="61">Nota de Credito</option>
                    <option value="56">Nota de Debito</option>
                </select>
            </div>

            {{-- Folio del DTE --}}
            <div class="form-group">
                <input type="number" name="folio" class="form-control" placeholder="Folio del DTE" id="exampleCheck1">
            </div>

            {{-- Fecha de Emision --}}
            <div class="form-group">
                <label for="rut">Fecha de Emision</label>
                <input class="form-control" placeholder="Fecha" max='2100-01-01' type="date" id="date" name="date">
            </div>

            {{-- Monto Total --}}
            <div class="form-group">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">$</div>
                    </div>
                    <input type="number" name="amount" class="form-control" min="1" step="any" placeholder="Monto total"
                        id="exampleCheck1">
                </div>
            </div>

            {{-- Boton --}}
            <button type="submit" class="btn btn-primary">Buscar Documento</button>
        </form>
    </div>

    <script>
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();
        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }
        today = yyyy + '-' + mm + '-' + dd;
        document.getElementById("date").setAttribute("max", today);
    </script>
@endsection
