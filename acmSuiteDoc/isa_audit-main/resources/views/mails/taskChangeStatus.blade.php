@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://www.acmsuite.com/'])
<img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
@endcomponent
@endslot
    
# Cambio de estatus de tareas.

{{-- Body --}}

Hola, <b>{{ $data['full_name'] }}</b>, te informamos que la siguiente {{ $data['type_task'] }} fue
{{ $data['approve'] }} por el responsable de la aprobación:

@component('mail::panel')
    **Planta:** {{ $data['corp_tradename'] }}  
    **Evaluación:** {{ $data['audit_processes'] }}  
    **Período:** {{ $data['init_date_format'] }} - {{ $data['end_date_format'] }}  
    **Sección:** Plan de Acción de {{ $data['origin'] }}  
    **No. Requerimiento:** {{ $data['no_requirement'] }}  
    **Requerimiento:** {{ $data['requirement'] }}  
    **{{ $data['type_task'] }}:** {{ $data['task'] }}  
    **Fecha de finalización:** {{ $data['close_date'] }}  
    **Estatus de tarea:** {{ $data['status'] }}  
    **Días Restantes:** {{ $data['days'] }}
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