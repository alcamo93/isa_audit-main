@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://www.acmsuite.com/'])
<img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
@endcomponent
@endslot
    
# Recordatorio de Vencimiento de Contrato

{{-- Body --}}

Hola <b>{{ $data['full_name'] }}</b>, tu contrato esta por expirar, revisa con tu administrador de cuenta la renovación o bien descargar 
tu copia de documentos cargados. Solo podrás acceder a tu cuenta para descargar tus documentos hasta la fecha de terminación de contrato.

@component('mail::panel')
    **Cliente:** {{ $data['cust_tradename'] }}  
    **Planta:** {{ $data['corp_tradename'] }}  
    **Contrato:** {{ $data['contract'] }}  
    **Licencia:** {{ $data['license']}}  
    **Fecha de inicio de contrato:** {{ $data['start_date'] }}  
    **Fecha de fin de contrato:** {{ $data['end_date'] }}  
    **Días restantes:** {{ $data['days'] }}  
    @component('mail::button', [ 'url' => $data['path'] ])
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