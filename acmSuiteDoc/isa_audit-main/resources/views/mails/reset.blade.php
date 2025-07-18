@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://www.acmsuite.com/'])
<img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
@endcomponent
@endslot

{{-- Body --}}

Hola <b>{{ (isset($data['name']) ? $data['name'] : '') }}</b>, has solicitado recientemente tu recuperación o cambio de contraseña de ACM Suite para la auditoría.

Por favor da click en la siguiente dirección para continuar con el proceso:
* Link: {{ (isset($data['resetPath']) ? $data['resetPath'] : '') }}


@component('mail::button', ['url' => asset('/')])
Acceso
@endcomponent

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} <b>ACM Suite</b>. Todos los derechos reservados.
@endcomponent
@endslot
@endcomponent
