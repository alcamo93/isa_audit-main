<table>
  <tbody>
    <tr>
      <td></td>
      <td colspan="21"></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="21">
        {{ $data['header'] }}
      </td>
    </tr>
    <tr>
      <td></td>
      <td colspan="21">
        {{ $data['subheader'] }}
      </td>
    </tr>
    <tr>
      <td></td>
    </tr>
    @for($i = 0; $i <= 13; $i++)
    @if($i == 0)
    <tr>
      <td></td>
      <td colspan="6">{{ $data['place']['name'] }}</td>
    </tr>
    @endif
    <tr>
      <td></td>
      <td colspan="6"></td>
    </tr>
    @if($i == 13)
    <tr>
      <td></td>
      <td colspan="6">{{ $data['place']['address'] }}</td>
    </tr>
    @endif
    @endfor
  </tbody>
</table>