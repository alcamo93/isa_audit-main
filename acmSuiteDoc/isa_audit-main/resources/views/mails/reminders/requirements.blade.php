@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://www.acmsuite.com/'])
<img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
@endcomponent
@endslot
    
# Recordatorio para Requerimientos

{{-- Body --}}

Hola <b>{{ $data['full_name'] }}</b>, a continuación aparece el reporte de recordatorios de requerimientos por vencer.

@foreach($data['actions'] as $action)
@component('mail::panel')
    **Planta:** {{ $action['corp_tradename'] }}  
    **Evaluación:** {{ $action['audit_processes'] }}  
    **Período:** {{ $action['init_date_format'] }} - {{ $action['end_date_format'] }}  
    **Sección:** Plan de acción de {{ $action['type'] }}  
    **No. Requerimiento:** {{ $action['no_requirement'] }}  
    **Requerimiento:** {{ $action['requirement'] }}  
    **Fecha de Inicio:** {{ $action['start_date'] }}  
    **Fecha de Vencimiento:** {{ $action['close_date'] }}  
    **Días Restantes:** {{ $action['days'] }}  
    @component('mail::button', [ 'url' => $action['path'] ])
        Ver
    @endcomponent
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

