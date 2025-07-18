@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://www.acmsuite.com/'])
<img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
@endcomponent
@endslot
    
# Recordatorio Vencimiento Permisos Críticos

{{-- Body --}}

Hola <b>{{ $data['full_name'] }}</b>, a continuación aparece el reporte de recordatorios de permisos críticos.

@foreach($data['obligations'] as $obligation)
@component('mail::panel')
    **Planta:** {{ $obligation['corp_tradename'] }}  
    **Evaluación:** {{ $obligation['audit_processes'] }}  
    **Período:** {{ $obligation['init_date_format'] }} - {{ $obligation['end_date_format'] }}  
    **Sección:** Permisos críticos  
    **No. Requerimiento:** {{ $obligation['no_requirement'] }}  
    **Requerimiento:** {{ $obligation['requirement'] }}  
    **Documento/evidencia cargada:** {{ $obligation['file_name'] }}  
    **Fecha de Expedición:** {{ $obligation['start_date'] }}  
    **Fecha de Vencimiento:** {{ $obligation['close_date'] }}  
    **Días Restantes:** {{ $obligation['days'] }}  
    **Estatus:** {{ $obligation['status'] }}  
    @component('mail::button', [ 'url' => $obligation['path'] ])
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

