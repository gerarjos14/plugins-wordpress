@component('mail::message')

# Hola {{$name_user}}

<hr>

# Se genero un error al crear {{$type}}.

{{$msj}}

@component('mail::button', ['url' => $url])
Ver en Billconnector
@endcomponent
<hr>
Gracias,<br>
{{ config('app.name') }}
@endcomponent

