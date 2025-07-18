
<table>
    <thead>
        <tr>
            <th colspan="22"></th>
        </tr>
        <tr>
            <th colspan="3"></th>
            <th colspan="2"></th>
            <th colspan="8" 
                style="
                font-size: 28px; 
                font-family:sans-serif; 
                color:#003e52;
                ">Permisos Críticos</th>
            <th colspan="4"></th>
        </tr>
        <tr>
            <th colspan="22"></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="22"></th>
        </tr>
        @foreach($data['matters'] as $matter)
        <tr>
            <th colspan="6"></th>
            <th colspan="4" style="font-size: 18px; font-family:sans-serif; font-weight:bold;">{{ $matter['matter'] }}</th><!-- Color de ambiental -->
            <th colspan="6"></th>
        </tr>
        <tr>
            <th colspan="2"></th>
            <th colspan="6" style="font-size: 12px; font-family:sans-serif; font-weight:bold; color:#767171;">Aspecto</th>
            <th colspan="6" style="font-size: 12px; font-family:sans-serif; font-weight:bold; color:#767171;">Cumplimiento /100</th>
            <th colspan="2"></th>
        </tr>
        @foreach($matter['aspects'] as $aspect)
        <tr>
            <th colspan="2"></th>
            <th colspan="6" style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $aspect['aspect'] }}</th>
            <th colspan="6" style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ round($aspect['percentage'], 2) }}%</th>
            <th colspan="2"></th>
        </tr>
        @endforeach
        <tr>
            <th colspan="2"></th>
            <th colspan="6" style="font-size: 12px; font-family:sans-serif; color:#767171;">Total</th>
            <th colspan="6" style="font-size: 12px; font-family:sans-serif; font-weight:bold;">{{ round($matter['percentage'], 2) }}%</th><!-- Color en caso de estar cerca a termianr-->
            <th colspan="2"></th>
        </tr>
        @endforeach
    </tbody>
</table>