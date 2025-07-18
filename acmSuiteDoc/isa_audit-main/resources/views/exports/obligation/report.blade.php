
<table>
    <thead>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr>
            <th></th>
            <th colspan="11" style="font-size: 18px; font-family:sans-serif; color:#003e52;">Reporte de Estatus de Permisos Críticos:</th>
        </tr>
        <tr>
            <th></th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Materia Legal</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Aspecto</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Nº de Requerimiento</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Requerimiento Legal</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Fundamento Legal</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Nivel de Riesgo</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Fecha de Expedición</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Fecha de Vencimiento</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Periodicidad</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Estatus</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Área</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">Responsable</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <th></th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $item['matter'] }}</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $item['aspect'] }}</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $item['no_requirement'] }}</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $item['requirement'] }}</th>
            <th>
                {!! $item['legals_name'] !!}
                <a href="{{ $item['link_legal'] }}" target="_blank">
                    Conoce más dando clic aquí
                </a>
            </th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">
                @foreach($item['risk'] as $risk)
                    {{ $risk }}<br>
                @endforeach
            </th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $item['init_date_format']}}</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $item['end_date_format']}}</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $item['periodicity'] }}</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $item['status'] }}</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $item['scope'] }}</th>
            <th style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $item['user'] }}</th>
        </tr>
        @endforeach
    </tbody>
</table>
