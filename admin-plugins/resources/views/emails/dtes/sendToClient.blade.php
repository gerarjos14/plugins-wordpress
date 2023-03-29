@component('mail::message')

# Hola {{$name_user}}

<hr>

# Le enviamos su Comprobante electronico. Haga click en el boton para poder acceder a el.

@component('mail::button', ['url' => $url])
Ver en Billconnector
@endcomponent
<hr>
Gracias,<br>
{{ config('app.name') }}
@endcomponent
