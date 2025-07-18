<table>
  <tbody>
    <tr>
      <td></td>
      <td><!-- space --></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="14">{{ $data['header'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="14">{{ $data['subheader'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="14"><!-- logo --></td>
    </tr>
    <tr>
      <td></td>
      <td>Total Actual</td>
      <td>Año</td>
      @foreach($data['total']['months'] as $month)
      <td>{{ $month['key'] }}</td>
      @endforeach
    </tr>
    <tr>
      <td></td>
      <td>{{ $data['total']['total'] }}%</td>
      <td>{{ $data['year'] }}</td>
      @foreach($data['total']['months'] as $month)
      <td>{{ $month['total'] }}</td>
      @endforeach
    </tr>
    <tr></tr>
    @foreach($data['matters'] as $matter)
    <tr>
      <td></td>
      <td><!-- image --></td>
      <td colspan="11">{{ $matter['matter'] }}</td>
      <td colspan="2">{{ $matter['total'] }}%</td>
    </tr>
    @foreach($matter['aspects'] as $aspect)
    <tr>
      <td></td>
      <td>{{ $aspect['aspect'] }}</td>
      <td><!-- year column --></td>
      @foreach($aspect['months'] as $month)
      <td>{{ $month['total'] }}%</td>
      @endforeach
    </tr>
    @endforeach
    <tr>
      <td></td>
      <td>Promedio (Total)</td>
      <td><!-- year column --></td>
      @foreach($matter['months'] as $month)
      <td>{{ $month['total'] }}%</td>
      @endforeach
    </tr>
    <tr><!-- space --></tr>
    @endforeach
  </tbody>
</table>