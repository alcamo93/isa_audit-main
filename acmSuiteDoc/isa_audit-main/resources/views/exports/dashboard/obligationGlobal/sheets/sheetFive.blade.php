<table>
  <tbody>
    <tr>
      <td></td>
      <td colspan="7"><!-- logo --></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="7">{{ $data['header'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="7">{{ $data['subheader'] }}</td>
    </tr>
    <tr></tr>
    <tr>
      <td></td>
      <td>Planta</td>
      <td>Año</td>
      <td>Total</td>
      @foreach($data['name_matters'] as $column)
        <td>{{ $column['matter'] }}</td>
      @endforeach
    </tr>
    @foreach($data['projects'] as $project)
    <tr>
      <td></td>
      <td>{{ $project['name'] }}</td>
      <td>{{ $project['year'] }}</td>
      <td>{{ $project['total_expired'] }}</td>
      @foreach($data['name_matters'] as $column)
        <td>{{ $project[ $column['key'] ]['total_expired'] }}</td>
      @endforeach
    </tr>
    @endforeach
  </tbody>
</table>