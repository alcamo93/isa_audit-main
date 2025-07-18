@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://www.acmsuite.com/'])
<img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
@endcomponent
@endslot
    
# Recordatorio para Tareas

{{-- Body --}}

Hola <b>{{ $data['full_name'] }}</b>, a continuación aparece el reporte de recordatorios que se han progaramado de tareas y subtareas por vencer.  

@foreach($data['tasks'] as $task)
@component('mail::panel')
    **Planta:** {{ $task['corp_tradename'] }}  
    **Evaluación:** {{ $task['audit_processes'] }}  
    **Período:** {{ $task['init_date_format'] }} - {{ $task['end_date_format'] }}  
    **Sección:** {{ $task['origin'] }}  
    **No. Requerimiento:** {{ $task['no_requirement'] }}  
    **Requerimiento:** {{ $task['requirement'] }}  
    **{{ $task['type'] }}:** {{ $task['body'] }}  
    **Fecha de finalización:** {{ $task['close_date'] }}  
    **Días restantes:** {{ $task['days'] }}  
    @component('mail::button', [ 'url' => $task['path'] ])
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

