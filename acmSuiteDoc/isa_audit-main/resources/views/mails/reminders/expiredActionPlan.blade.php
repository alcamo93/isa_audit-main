@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://www.acmsuite.com/'])
<img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
@endcomponent
@endslot

{{ '# '.$data['title'] }}

{{-- Body --}}

Hubo un cambio en la fecha de cierre de <b>{{ $data['body']['realCloseDateOld'] }}</b> a <b>{{ $data['body']['realCloseDate'] }}</b> 
en el <b>{{ $data['body']['reqName'] }}</b> correspondiente a la auditoría <b>{{ $data['body']['auditName'] }}</b> 
por la 

## Causa: 
{{ $data['body']['cause'] }}

@component('mail::button', ['url' => asset($data['link']) ])
Revisar
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
