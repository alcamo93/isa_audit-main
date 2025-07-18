<table>
  <tbody>
    <tr>
      <td></td>
      <td colspan="14"><!-- logo --></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="14">{{ $data['header'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="14">{{ $data['subheader'] }}</td>
    </tr>
    <tr></tr>
    <tr>
      <td></td>
      <td>Planta</td>
      <td>Año</td>
      <td>Total</td>
      <td>ENE</td>
      <td>FEB</td>
      <td>MAR</td>
      <td>ABR</td>
      <td>MAY</td>
      <td>JUN</td>
      <td>JUL</td>
      <td>AGO</td>
      <td>SEP</td>
      <td>OCT</td>
      <td>NOV</td>
      <td>DIC</td>
    </tr>
    @foreach($data['historical'] as $project)
    <tr>
      <td></td>
      <td>{{ $project['name'] }}</td>
      <td>{{ $project['year'] }}</td>
      <td>{{ $project['total'] }}%</td>
      @foreach($project['months'] as $hitorical)
      <td>{{ $hitorical['total'] }}%</td>
      @endforeach
    </tr>
    @endforeach
  </tbody>
</table>