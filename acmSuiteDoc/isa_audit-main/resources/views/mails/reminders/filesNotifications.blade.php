@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://www.acmsuite.com/'])
<img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
@endcomponent
@endslot
    
# Recordatorio periódico de evidencias por vencer

{{-- Body --}}

Hola <b>{{ $data['full_name'] }}</b>, a continuación se lista el reporte de recordatorios de evidencias por vencer.

@foreach($data['files'] as $file)
@component('mail::panel')
    {{-- **Cliente:** {{ $file['cust_tradename'] }}   --}}
    **Planta:** {{ $file['corp_tradename'] }}  
    **Evaluación:** {{ $file['audit_processes'] }}  
    **Período:** {{ $file['evaluation_init_date_format'] }} - {{ $file['evaluation_end_date_format'] }}  
    **No. Requerimiento:** {{ $file['no_requirement'] }}  
    **Requerimiento:** {{ $file['requirement'] }}  
    **Documento/evidencia cargada:** {{ $file['library_name'] }}  
    **Fecha de Expedición:** {{ $file['init_date_format'] }}  
    **Fecha de Vencimiento:** {{ $file['end_date_format'] }}  
    **Días Restantes:** {{ $file['days'] }}  
    @component('mail::table')
        | Sección     | Estatus     |  
        |:-----------:|:-----------:|  
        @foreach($file['all_status'] as $status)
            | {{ $status['section_name'] }} | {{ $status['status'] }} | 
        @endforeach
    @endcomponent
@endcomponent
@component('mail::button', [ 'url' => $file['path'] ])
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