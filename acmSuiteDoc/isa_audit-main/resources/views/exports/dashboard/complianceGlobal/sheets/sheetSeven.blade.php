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
    @foreach($data['matters'] as $matter)
    <!-- 5 -->
    <tr> 
      <td></td>
      <td colspan="12">{{ $matter['matter'] }}</td>
      <td colspan="2">{{ $matter['total'] }}%</td>
    </tr>
    <tr>
      <td></td>
      <td>Planta</td>
      <td>Año</td>
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
    @foreach($matter['historical'] as $project)
    <tr>
      <td></td>
      <td>{{ $project['name'] }}</td>
      <td>{{ $project['year'] }}</td>
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
    <tr></tr>
    @endforeach
  </tbody>
</table>