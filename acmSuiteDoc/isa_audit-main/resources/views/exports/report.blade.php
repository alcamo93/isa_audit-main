
<table>
    <thead>
        <tr>
            <th colspan="22"></th>
        </tr>
        <tr>
            <th colspan="2"></th>
            <th colspan="{{ (($risk == true) ? 15 : 12) }}" style="font-size: 18px; font-family:sans-serif; color:#003e52;">Descripción de Auditoría:</th>
        </tr>
        <tr>
            <th colspan="2"></th>
            <th colspan="3" style="font-size: 12px; font-family:sans-serif; color:#767171;">Nº de Hallazgo</th>
            <th colspan="3" style="font-size: 12px; font-family:sans-serif; color:#767171;">Descripción del Hallazgo</th>
            <th colspan="3" style="font-size: 12px; font-family:sans-serif; color:#767171;">Fundamento Legal</th>
            <th colspan="3" style="font-size: 12px; font-family:sans-serif; color:#767171;">Recomendación</th>
            @if ($risk == true)
                <th colspan="3" style="font-size: 12px; font-family:sans-serif; color:#767171;">Clasificación de Riesgo</th>
            @else
            @endelse
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $n)
        <tr>
            <th colspan="2"></th>
            <th colspan="3" style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $n['num'] }}</th>
            <th colspan="3" style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $n['description'] }}</th>
            <th colspan="3" style="font-size: 12px; font-family:sans-serif; color:#767171;">
                {!! $n['legal_name'] !!}
                <a href="{{ $n['legal'] }}" target="_blank">Conoce más dando clic aquí</a>
            </th>
            <th colspan="3" style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $n['finding'] }}</th>
            @if ($risk == true)
                <th colspan="3" style="font-size: 12px; font-family:sans-serif; color:#767171;">
                @foreach($n['risk'] as $r)
                    {{ $r['risk_category'] }}: {{ $r['interpretation'] }}<br>
                @endforeach
                </th>
            @else
            @endelse
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
