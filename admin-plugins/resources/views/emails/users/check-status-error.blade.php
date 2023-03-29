@component('mail::message')
# Sii notifica que el dte se proceso con errores

DTE type #{{$dte->type}}, folio #{{$dte->folio}}

{{$error}}.

@component('mail::button', ['url' => url('/dte/' . $dte->uuid)])
Ver
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
