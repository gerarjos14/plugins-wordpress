@if (empty($data))
    {{ 'Error' }}
@else
    @include('customer.beon24.index')
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>

    <link rel="stylesheet" href="{{ asset('css/beon24/success.css') }}">

    <div class="row" style="margin: auto !important;">
        <div class="col-md-12 col-success-bc">
            <div class="card card-success-bc">
                <div class="card-body">
                    <i class='fas fa-check-circle icon-success'></i>

                    <h1>¡Listo! <br class="break-success"> Se acreditó tu pago</h1>
                    <h3> Has comprado el plan {{ $data['plan']['name'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-info">
            <div class="card card-detail-buy">
                <div class="card-body text-bc-data-buy">
                    Detalle de la compra:
                    <table class="form-table p-2 table-info-buy" id="table-buy-data">
                        <tr>
                            <th class="row-title text-bc-data-buy">Nombre del plan</th>
                            <th class="espacio-width"></th>
                            <th class="row-title text-bc-data-buy">{{ $data['plan']['name'] }}</th>
                        </tr>
                        <tr class="espacio"></tr>
                        <tr>
                            <th class="row-title text-bc-data-buy">Precio</th>
                            <th class="espacio-width"></th>
                            <th class="row-title text-bc-data-buy">{{ $data['plan']['currency'] }} $
                                {{ $data['plan']['amount'] }}</th>
                        </tr>
                        <tr class="espacio"></tr>
                        <tr>
                            <th class="row-title text-bc-data-buy">Fecha</th>
                            <th class="espacio-width"></th>
                            <th class="row-title text-bc-data-buy">{{ $data['date_buy'] }}</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-bc-redirect">
            <p class="text-bc-redirect">
                Serás redirigido a tu tienda en <span id="countdown"> </span> segundos,
                si tienes problemas, haz clic en
            </p>
            <a href="{{ $data['url_redirect'] }}" class="btn btn-primary btn-redirect">
                Volver a mi tienda
                @if ($data['is_wordpress'])
                    Wordrpess
                @endif
            </a>
        </div>
    </div>

    <script>
        var url_redirect = "{{ $data['url_redirect'] }}";

        setTimeout(() => {
            window.location.href = url_redirect;
        }, 10000);

        window.onload = updateClock;

        var totalTime = 10;

        function updateClock() {
            document.getElementById('countdown').innerHTML = totalTime;
            if (totalTime == 0) {
                console.log('Final');
            } else {
                totalTime -= 1;
                setTimeout("updateClock()", 1000);
            }
        }
    </script>
@endif
