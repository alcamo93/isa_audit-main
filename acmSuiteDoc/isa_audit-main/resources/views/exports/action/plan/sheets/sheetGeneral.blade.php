<table>
  <tbody>
    <tr>
      <td></td>
      <td colspan="18"><!-- logo --></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="18">{{ $data['header'] }}</td>
    </tr>
    <tr>
      <td></td>
      <td colspan="18">{{ $data['subheader'] }}</td>
    </tr>
    <tr></tr>
    <tr>
      <td></td>
      <td colspan="2">Cliente</td>
      <td colspan="6">{{ $data['headers']['corp_tradename'] }}</td>
      <td></td>
      <td></td>
      <td colspan="2">Calle</td>
      <td colspan="5">{{ $data['headers']['street'] }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">Razón social</td>
      <td colspan="6">{{ $data['headers']['corp_trademark'] }}</td>
      <td></td>
      <td></td>
      <td colspan="2">Colonia</td>
      <td colspan="5">{{ $data['headers']['suburb'] }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">RFC</td>
      <td colspan="6">{{ $data['headers']['rfc'] }}</td>
      <td></td>
      <td></td>
      <td colspan="2">Ciudad</td>
      <td colspan="5">{{ $data['headers']['city'] }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">Auditor(es) / Resposable(es)</td>
      <td colspan="6">{{ $data['headers']['users'] }}</td>
      <td></td>
      <td></td>
      <td colspan="2">Estado</td>
      <td colspan="5">{{ $data['headers']['state'] }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">Giro</td>
      <td colspan="6">{{ $data['headers']['industry'] }}</td>
      <td></td>
      <td></td>
      <td colspan="2">País</td>
      <td colspan="5">{{ $data['headers']['country'] }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">Aspectos evaluados</td>
      <td colspan="6">{{ $data['headers']['aspects'] }}</td>
      <td></td>
      <td></td>
      <td colspan="2">Período</td>
      <td colspan="5">{{ $data['headers']['period'] }}</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2">Alcance</td>
      <td colspan="6">{{ $data['headers']['scope'] }}</td>
      <td></td>
      <td></td>
      <td colspan="2">Estatus</td>
      <td colspan="5">{{ $data['headers']['status'] }}</td>
      <td></td>
    </tr>
    <tr></tr>
    <!-- headers -->
    <tr>
      <td></td>
      <td colspan="4">
        {{ $data['action_plan']['header_requirements'] }}
      </td>
      <td></td>
      <td colspan="{{ ( sizeof($data['action_plan']['header_table_tasks']) * 2 ) + 2 }}">
        {{ $data['action_plan']['header_tasks'] }}
      </td>
    </tr>
    <!-- headers table-->
    <tr>
      <td></td>
      <td colspan="4">
        {{ $data['action_plan']['header_table_requirement'] }}
      </td>
      <td></td>
      <td colspan="2"></td>
      @foreach($data['action_plan']['header_table_tasks'] as $headerTable)
      <td colspan="2">
        {{ $headerTable['status'] }}
      </td>
      @endforeach
    </tr>
    <!-- body table -->
    @foreach( $data['action_plan']['matters'] as $matter )
    <tr>
      <td></td>
      <td colspan="2">
        {{ $matter['matter'] }}
      </td>
      <td colspan="2">
        {{ $matter['total'] }}%
      </td>
      <td></td>
      <td colspan="2">
        {{ $matter['matter'] }}
      </td>
      @foreach( $matter['count_status'] as $column )
      <td colspan="2">
        {{ $column['total'] }}%
      </td>
      @endforeach
    </tr>
    @endforeach
  </tbody>
</table>