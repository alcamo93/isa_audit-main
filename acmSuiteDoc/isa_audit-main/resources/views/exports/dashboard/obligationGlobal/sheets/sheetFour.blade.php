<table>
  <tbody>
    <tr></tr>
    <tr>
      <td></td>
      <td colspan="12">{{ $data['header'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="12">{{ $data['subheader'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="12"><!-- logo --></td>
    </tr>
    @foreach($data['matters'] as $matter)
    <tr>
      <td></td>
      <td colspan="12">{{ $matter['matter'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="6">Aspecto</td>
      <td colspan="6">Cumplimiento /100%</td>
    </tr>
    @foreach($matter['aspects'] as $aspect)
    <tr>
      <td></td>
      <td colspan="6">{{ $aspect['aspect'] }}</td>
      <td colspan="6">{{ $aspect['total'] }}%</td>
    </tr>
    @endforeach
    <tr>
      <td></td>
      <td colspan="6">Total</td>
      <td colspan="6">{{ $matter['total'] }}%</td>
    </tr>
    <tr></tr>
    @endforeach
  </tbody>
</table>