<table>
  <tbody>
    <tr>
      <td></td>
      <td colspan="16"><!-- logo --></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="16">{{ $data['header'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="16">{{ $data['subheader'] }}</td>
    </tr>
    <tr></tr>
    <tr>
      <td></td>
      <td colspan="2">Cliente</td>
      <td colspan="4">{{ $data['headers']['corp_tradename'] }}</td>
      <td colspan="2">Calle</td>
      <td colspan="4">{{ $data['headers']['street'] }}</td>
      <td colspan="2">Fecha</td>
      <td colspan="2">{{ $data['headers']['date'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">Razón social</td>
      <td colspan="4">{{ $data['headers']['corp_trademark'] }}</td>
      <td colspan="2">Colonia</td>
      <td colspan="4">{{ $data['headers']['suburb'] }}</td>
      <td colspan="2">Estatus</td>
      <td colspan="2">{{ $data['headers']['status'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">RFC</td>
      <td colspan="4">{{ $data['headers']['rfc'] }}</td>
      <td colspan="2">Ciudad</td>
      <td colspan="4">{{ $data['headers']['city'] }}</td>
      <td colspan="2">Alcance</td>
      <td colspan="2">{{ $data['headers']['scope'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">Auditor(es) / Resposable(es)</td>
      <td colspan="4">{{ $data['headers']['users'] }}</td>
      <td colspan="2">Estado</td>
      <td colspan="4">{{ $data['headers']['state'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">Giro</td>
      <td colspan="4">{{ $data['headers']['industry'] }}</td>
      <td colspan="2">País</td>
      <td colspan="4">{{ $data['headers']['country'] }}</td>
    </tr>
    <tr></tr>
    <tr>
      <td></td>
      <td colspan="5">{{ $data['global']['totals']['title'] }}</td>
      <td colspan="2">{{ $data['global']['totals']['total'] }}%</td>
      <td colspan="2"></td>
      <td colspan="5">{{ $data['global']['total_counts']['title'] }}</td>
      <td colspan="2">{{ $data['global']['total_counts']['total'] }}</td>
    </tr>
    <tr></tr>
    <tr>
      <td></td>
      <td colspan="7">{{ $data['global']['totals']['subtitle'] }}</td>
      <td colspan="2"></td>
      <td colspan="7">{{ $data['global']['total_counts']['subtitle'] }}</td>
    </tr>
    @foreach($data['matters'] as $matter)
    <tr>
      <td></td>
      <td colspan="1"><!-- image logo --></td>
      <td colspan="4">{{ $matter['matter'] }}</td>
      <td colspan="2">{{ $matter['total'] }}%</td>
      <td colspan="2"></td>
      <td colspan="1"><!-- image logo --></td>
      <td colspan="4">{{ $matter['matter'] }}</td>
      <td colspan="2">{{ $matter['total_count'] }}</td>
    </tr>
    @endforeach
  </tbody>
</table>