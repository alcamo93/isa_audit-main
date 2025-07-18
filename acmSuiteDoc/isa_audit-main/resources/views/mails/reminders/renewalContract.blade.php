@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://www.acmsuite.com/'])
<img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
@endcomponent
@endslot
    
# Notificación de Renovación de Contrato

{{-- Body --}}

Hola <b>{{ $data['full_name'] }}</b>, tu contrato ha sido renovado, a continuación, se presenta la información correspondiente:

@component('mail::panel')
    **Empresa:** {{ $data['cust_tradename'] }}  
    **Planta(s):** {{ $data['corp_tradename'] }}  
    **Contrato:** {{ $data['contract'] }}  
    **Licencia:** {{ $data['license']}}  
    **Fecha de inicio de contrato:** {{ $data['start_date'] }}  
    **Fecha de fin de contrato:** {{ $data['end_date'] }}  
    **Días restantes:** {{ $data['days'] }}  
    @component('mail::button', ['url' => $data['path']])
        Ver
    @endcomponent
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
        © {{ date('Y') }} **ACM Suite**. Todos los derechos reservados.
    @endcomponent
@endslot
@endcomponent