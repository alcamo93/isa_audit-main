
<table>
    <thead>
        <tr>
            <th colspan="22"></th>
        </tr>
        <tr>
            <th colspan="5"></th>
            <th colspan="2"></th>
            <th colspan="5" 
                style="
                font-size: 50px; 
                font-family:sans-serif; 
                color:#003e52;
                ">Hallazgos</th>
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
        @foreach($data as $m)
        <tr>
            <th colspan="6"></th>
            <th colspan="4" style="font-size: 18px; font-family:sans-serif; font-weight:bold; color:{{ $m['color'] }}">{{ $m['matter'] }}</th><!-- Color de ambiental -->
            <th colspan="6"></th>
        </tr>
        @foreach($m['aspects'] as $a)
        <tr>
            <th colspan="2"></th>
            <th colspan="6" style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $a['aspect'] }}</th>
            <th colspan="6" style="font-size: 12px; font-family:sans-serif; color:#767171;">{{ $a['count'] }}</th>
            <th colspan="2"></th>
        </tr>
        @endforeach
        <tr>
            <th colspan="2"></th>
            <th colspan="6" style="font-size: 12px; font-family:sans-serif; color:#767171;">Total</th>
            <th colspan="6" style="font-size: 12px; font-family:sans-serif; font-weight:bold; color:{{ $m['color'] }};">{{ $m['total'] }}</th><!-- Color en caso de estar cerca a termianr-->
            <th colspan="2"></th>
        </tr>
        @endforeach
    </tbody>
</table>