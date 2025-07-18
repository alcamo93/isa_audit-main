<table>
  <tbody>
    <tr>
      <td></td>
      <td colspan="21"></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="21">
        {{ $data['main']['header'] }}
      </td>
    </tr>
    <tr>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="4">{{ $data['main']['place']['name'] }}</td>
      <td><!-- space --></td>
      @foreach($data['main']['matters'] as $matter)
      <td colspan="{{ intval( 16 / sizeof($data['main']['matters']) ) }}">
        {{ $matter['matter'] }}
      </td>
      @endforeach
    </tr>
    <tr>
      <td></td>
      <td colspan="4"><!-- image --></td>
      <td><!-- space --></td>
      @foreach($data['main']['matters'] as $matter)
      <td colspan="{{ intval( 16 / sizeof($data['main']['matters']) ) }}">
        <!-- image -->
      </td>
      @endforeach
    </tr>
    <tr>
      <td></td>
      <td colspan="4">{{ $data['main']['place']['address'] }}</td>
      <td><!-- space --></td>
      @foreach($data['main']['matters'] as $matter)
      <td colspan="{{ intval( 16 / sizeof($data['main']['matters']) ) }}">
        {{ $matter['total'] }}%
      </td>
      @endforeach
    </tr>
    <tr></tr>
    <!-- row: 25 - 7 = 18 -->
    @for($i = 0; $i < 18; $i++)
    <tr></tr>
    @endfor
    <tr>
      <td></td>
      <td colspan="21">
        {{ $data['historical']['header'] }}
      </td>
    </tr>
    <tr>
      <td></td>
      <td colspan="21">
      {{ $data['historical']['subheader'] }}
      </td>
    </tr>
  </tbody>
</table>