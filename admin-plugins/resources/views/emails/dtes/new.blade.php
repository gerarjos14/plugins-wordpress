@component('mail::message')
# Usted ha recibido un nuevo dte

Para visualizar la información.

@component('mail::button', ['url' => url('/dte/' . $dte->uuid)])
Abrir
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
