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
    <td>No. de Requerimiento</td>
    <td>Requerimiento Legal</td>
    @if($data['with_level_risk'])
    <td>Nivel de Riesgo</td>
    @endif
    <td>Prioridad</td>
    <td>Tipo</td>
    <td>Tarea</td>
    <td>Fecha de inicio</td>
    <td>Fecha de vencimiento</td>
    <td>Estatus</td>
    <td>Responsable de aprobación</td>
    <td>Responsable de tarea/subtarea</td>
    <td>Comentarios</td>
  </tr>
  @foreach($data['records'] as $record)
  <tr>
    <td></td>
    <td> {{ $record['matter'] }} </td>
    <td> {{ $record['aspect'] }} </td>
    <td> {{ $record['no_requirement'] }} </td>
    <td> {{ $record['requirement'] }} </td>
    @if($data['with_level_risk'])
    <td>
      @foreach($record['risk_totals'] as $risk)
        {{ $risk }} <br>
      @endforeach
    </td>
    @endif
    <td> {{ $record['priority'] }} </td>
    <td> {{ $record['type_task'] }} </td>
    <td> {{ $record['task'] }} </td>
    <td> {{ $record['init_date_format'] }} </td>
    <td> {{ $record['close_date_format'] }} </td>
    <td> {{ $record['status'] }} </td>
    <td> {{ $record['user_ap'] }} </td>
    <td> {{ $record['user_task'] }} </td>
    <td>
      @foreach($record['comments'] as $comment)
        {{ $comment }} <br>
      @endforeach
    </td>
  </tr>
  @endforeach
</table>