<table>
  <tbody>
    <tr>
      <td></td>
      <td colspan="15"><!-- logo --></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="15">{{ $data['header'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="15">{{ $data['subheader'] }}</td>
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
      <td>{{ $project['ENE']['total'] }}%</td>
      <td>{{ $project['FEB']['total'] }}%</td>
      <td>{{ $project['MAR']['total'] }}%</td>
      <td>{{ $project['ABR']['total'] }}%</td>
      <td>{{ $project['MAY']['total'] }}%</td>
      <td>{{ $project['JUN']['total'] }}%</td>
      <td>{{ $project['JUL']['total'] }}%</td>
      <td>{{ $project['AGO']['total'] }}%</td>
      <td>{{ $project['SEP']['total'] }}%</td>
      <td>{{ $project['OCT']['total'] }}%</td>
      <td>{{ $project['NOV']['total'] }}%</td>
      <td>{{ $project['DIC']['total'] }}%</td>
    </tr>
    @endforeach
  </tbody>
</table>