@if (empty($card))
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

                    <h1>¡Listo! <br class="break-success"> Se ha activado tu tarjeta</h1>
                    <h3> Has activado la tarjeta *********  {{ $card->card_last_four }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-info">
            <div class="card card-detail-buy">
                <div class="card-body text-bc-data-buy">
                    Detalle de la tarjeta:
                    <table class="form-table p-2 table-info-buy" id="table-buy-data">
                        <tr>
                            <th class="row-title text-bc-data-buy">Últimos 4 dígitos</th>
                            <th class="espacio-width"></th>
                            <th class="row-title text-bc-data-buy">{{ $card->card_last_four }}</th>
                        </tr>
                        <tr>
                            <th class="row-title text-bc-data-buy">CVC</th>
                            <th class="espacio-width"></th>
                            <th class="row-title text-bc-data-buy">{{ base64_decode($card->cvc) }} </th>
                        </tr>
                        <tr class="espacio"></tr>
                        <tr class="espacio"></tr>
                        <tr>
                            <th class="row-title text-bc-data-buy">Marca</th>
                            <th class="espacio-width"></th>
                            <th class="row-title text-bc-data-buy">{{ $card->card_brand }} </th>
                        </tr>
                        <tr class="espacio"></tr>
                        <tr>
                            <th class="row-title text-bc-data-buy">Fecha de vencimiento</th>
                            <th class="espacio-width"></th>
                            <th class="row-title text-bc-data-buy">{{ base64_decode($card->month)}}/{{base64_decode($card->year) }}</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-bc-redirect">
            <p class="text-bc-redirect">
                Serás redirigido en <span id="countdown"> </span> segundos,
                si tienes problemas, haz clic en
            </p>
            <a href="{{ route('customer.cards.index') }}" class="btn btn-primary btn-redirect">
                Volver
            </a>
        </div>
    </div>

    <script>
        var url_redirect = "{{ route('customer.cards.index') }}";

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
