@if (empty($type_error))
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
                    <h1>No se pudo iniciar sesión en BillConnector</h1>
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
                Esta página se cerrará <span id="countdown"> </span> segundos
            </p>
        </div>
    </div>

    <script>
        setTimeout(() => {
            window.close();
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
