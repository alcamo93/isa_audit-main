@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://www.acmsuite.com/'])
<img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
@endcomponent
@endslot
    
# Recordatorio de Vencimiento de Contrato

{{-- Body --}}

Hola <b>{{ $data['full_name'] }}</b>, te enviamos el reporte de contratos por vencer para su renovación.

@foreach($data['contracts'] as $contract)
@component('mail::panel')
    **Empresa:** {{ $contract['cust_tradename'] }}  
    **Planta(s):** {{ $contract['corp_tradename'] }}  
    **Contrato:** {{ $contract['contract'] }}  
    **Licencia:** {{ $contract['license']}}  
    **Fecha de inicio de contrato:** {{ $contract['start_date'] }}  
    **Fecha de fin de contrato:** {{ $contract['end_date'] }}  
    **Días restantes:** {{ $contract['days'] }}  
@endcomponent
@component('mail::button', [ 'url' => $contract['path'] ])
    Ver
@endcomponent
@endforeach

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
        © {{ date('Y') }} **ACM Suite**. Todos los derechos reservados.
    @endcomponent
@endslot
@endcomponent