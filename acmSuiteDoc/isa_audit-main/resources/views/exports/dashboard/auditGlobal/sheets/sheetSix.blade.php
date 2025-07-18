<table>
  <tbody>
    <tr></tr>
    <tr>
      <td></td>
      <td colspan="{{ $numberColumns }}">{{ $data['header'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="{{ $numberColumns }}"><!-- logo --></td>
    </tr>
    <tr></tr>
    @foreach($data['matters'] as $matter)
    <tr>
      <td></td>
      <td colspan="{{ $numberColumns }}">{{ $matter['matter'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td>Aspecto</td>
      @foreach($data['columns_name'] as $column)
      <td>{{ $column['name'] }}</td>
      @endforeach
    </tr>
    @foreach($matter['aspects'] as $aspect)
    <tr>
      <td></td>
      <td>{{ $aspect['aspect'] }}</td>
      @foreach($data['columns_name'] as $column)
      <td>{{ $aspect['values'][ $column['key'] ]['total_count'] }}</td>
      @endforeach
    </tr>
    @endforeach
    <tr></tr>
    @endforeach
  </tbody>
</table>