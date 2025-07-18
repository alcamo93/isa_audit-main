
<table>
    <thead>
        <tr >
            <th colspan="24"></th>
        </tr>
        <tr >
            <th colspan="3"></th>
            <th colspan="2"></th>
            <th colspan="9" 
                style="
                font-size: 28px; 
                font-family:sans-serif; 
                color:#003e52;
                ">Reporte de permisos críticos</th>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th colspan="24"></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="1"></th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Cliente</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['corp_tradename'] }}</th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Calle</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['street'] }}</th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Fecha</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['date'] }}</th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Razón social</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['corp_trademark'] }}</th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Colonia</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['suburb'] }}</th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">RFC</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['rfc'] }}</th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Ciudad</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['city'] }}</th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Auditores/Responsable(s)</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['responsible'] }}</th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Estado</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['state'] }}</th>
        </tr>
        <tr>
            <th colspan="1"></th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Giro</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['industry'] }}</th>
            <th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">País</th>
            <th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['country'] }}</th>
        </tr>
        <tr >
            <th colspan="24"></th>
        </tr>
        <tr >
            <th colspan="1"></th>
            <th colspan="4" style="font-family:sans-serif; font-size: 18px; color:#003e52;">Cumplimiento Global</th>
            <th colspan="2" style="font-family:sans-serif; font-size: 18px; color:#ff5000;">{{ round($percentages['global_percentage'], 2) }}%</th>
            <th colspan="3"></th>
            <th colspan="7" style="font-family:sans-serif; font-size: 18px; color:#003e52;">Permisos Críticos vencidos/sin documento</th>
            <th colspan="2" style="font-family:sans-serif; font-size: 18px; color:#f7323f;">{{ $percentages['global_expired'] }}</th>
            <th colspan="3"></th>
        </tr>
        <tr >
            <th colspan="24"></th>
        </tr>
        <tr >
            <th colspan="1"></th>
            <th colspan="5"  style="font-family:sans-serif; font-size: 15px; color:#a6a6a6;">Detalle de Cumplimiento</th>
            <th colspan="6"></th>
            <th colspan="8"  style="font-family:sans-serif; font-size: 15px; color:#a6a6a6;">Detalle de permisos críticos vencidos o sin documento</th>
            <th colspan="3"></th>
        </tr>
        <tr >
            <th colspan="24"></th>
        </tr>
        @foreach($percentages['matters'] as $matter)
        <tr >
            <th colspan="1"></th>
            <th colspan="2"></th>
            <th colspan="3" style="font-family:sans-serif; font-size: 16px; color:#a6a6a6;">{{ $matter['matter'] }}</th>
            <th colspan="2" style="font-family:sans-serif; font-size: 16px;">{{ round($matter['percentage'], 2) }}%</th>
            <th colspan="3"></th>
            <th colspan="2"></th>
            <th colspan="4" style="font-family:sans-serif; font-size: 16px; color:#a6a6a6;">{{ $matter['matter'] }}</th>
            <th colspan="2" style="font-family:sans-serif; font-size: 16px;">{{ $matter['count'] }}</th>
            <th colspan="2"></th>
        </tr>
        <tr >
            <th colspan="24"></th>
        </tr>
        @endforeach
    </tbody>
</table>