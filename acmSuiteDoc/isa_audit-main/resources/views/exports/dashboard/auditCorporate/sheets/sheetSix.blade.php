<table>
  <tr>
    <td></td>
    <td colspan="14"><!-- logo --></td>
  </tr>
  <tr>
    <td></td>
    <td colspan="14">{{ $data['header'] }}  {{ $data['subheader'] }}</td>
  </tr>
  <tr>
    <td></td>
    <td>Materia Legal</td>
    <td>Aspecto</td>
    <td>No. de Hallazgo</td>
    <td>Requerimiento Legal</td>
    <td>Hallazgo/Recomendación</td>
    <td>Fundamento Legal</td>
    <td>Nivel de Riesgo</td>
  </tr>
  @foreach($data['records'] as $record)
  <tr>
    <td></td>
    <td>{{ $record['matter'] }}</td>
    <td>{{ $record['aspect'] }}</td>
    <td>{{ $record['no_requirement'] }}</td>
    <td>{{ $record['requirement'] }}</td>
    <td>{{ $record['finding'] }}</td>
    <td>
      {!! $record['legals_name'] !!}
      <a href="{{ $record['link_legal'] }}" target="_blank">
        Conoce más dando clic aquí
      </a>
    </td>
    <td>
      @foreach($record['risk'] as $risk)
        {{ $risk }} <br>
      @endforeach
    </td>
  </tr>
  @endforeach
</table>