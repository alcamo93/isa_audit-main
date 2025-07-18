<table>
  <tbody>
    <tr>
      <td></td>
      <td colspan="21"><!-- logo --></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="21">{{ $data['header'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="21">{{ $data['subheader'] }}</td>
    </tr>
    <tr></tr>
    @for($i = 0; $i <= 30; $i++)
    @if($i == 17)
    <tr>
      <td></td>
      <td colspan="8">{{ $data['place']['name'] }}</td>
    </tr>
    @endif
    <tr>
      <td></td>
      <td colspan="8"></td>
    </tr>
    @if($i == 30)
    <tr>
      <td></td>
      <td colspan="8">{{ $data['place']['address'] }}</td>
    </tr>
    @endif
    @endfor
  </tbody>
</table>