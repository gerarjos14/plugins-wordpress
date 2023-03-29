@if (empty($message))
    {{ 'Error' }}
@else
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    @include('customer.beon24.index')
    <link rel="stylesheet" href="{{ asset('css/beon24/error.css') }}">

    <div class="row" style="margin: auto !important;">
        <div class="col-md-12 col-error-bc">
            <div class="card card-error-bc">
                <div class="card-body">
                    <i class='fas fa-exclamation-circle icon-error'></i>

                    <h3>Algo salió mal...</h3>
                    <h1>No se pudo activar tu tarjeta</h1>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-info">
            <div class="card card-detail-buy">
                <div class="card-body text-bc-data-buy">
                    {{ $message }}
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
