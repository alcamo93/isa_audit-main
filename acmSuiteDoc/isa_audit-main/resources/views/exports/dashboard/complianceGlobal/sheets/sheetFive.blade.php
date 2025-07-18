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
      <td>{{ $project['total_count'] }}</td>
      <td>{{ $project['ENE']['total_count'] }}</td>
      <td>{{ $project['FEB']['total_count'] }}</td>
      <td>{{ $project['MAR']['total_count'] }}</td>
      <td>{{ $project['ABR']['total_count'] }}</td>
      <td>{{ $project['MAY']['total_count'] }}</td>
      <td>{{ $project['JUN']['total_count'] }}</td>
      <td>{{ $project['JUL']['total_count'] }}</td>
      <td>{{ $project['AGO']['total_count'] }}</td>
      <td>{{ $project['SEP']['total_count'] }}</td>
      <td>{{ $project['OCT']['total_count'] }}</td>
      <td>{{ $project['NOV']['total_count'] }}</td>
      <td>{{ $project['DIC']['total_count'] }}</td>
    </tr>
    @endforeach
  </tbody>
</table>