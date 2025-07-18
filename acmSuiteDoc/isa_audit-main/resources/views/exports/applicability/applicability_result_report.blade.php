<table>
	<thead>
		<tr>
			<th></th>
		</tr>
		<tr >
			<th></th>
			<th colspan="2"></th>
			<th colspan="6">{{ $title }}</th>
			<th colspan="2"></th>
		</tr>
		<tr>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th></th>
			<th>Cliente</th>
			<th colspan="3">{{ $data['headers']['corp_tradename'] }}</th>
			<th>Calle</th>
			<th colspan="2">{{ $data['headers']['street'] }}</th>
			<th>Fecha</th>
			<th colspan="2">{{ $data['headers']['date'] }}</th>
		</tr>
		<tr>
			<th></th>
			<th>Razón social</th>
			<th colspan="3">{{ $data['headers']['corp_trademark'] }}</th>
			<th>Colonia</th>
			<th colspan="2">{{ $data['headers']['suburb'] }}</th>
			<th>Estatus</th>
			<th colspan="2">{{ $data['headers']['status'] }}</th>
		</tr>
		<tr>
			<th></th>
			<th>RFC</th>
			<th colspan="3">{{ $data['headers']['rfc'] }}</th>
			<th>Ciudad</th>
			<th colspan="2">{{ $data['headers']['city'] }}</th>
			<th>Alcance</th>
			<th colspan="2">{{ $data['headers']['scope'] }}</th>
		</tr>
		<tr>
			<th></th>
			<th>Responsable</th>
			<th colspan="3">{{ $data['headers']['users'] }}</th>
			<th>Estado</th>
			<th colspan="2">{{ $data['headers']['state'] }}</th>
		</tr>
		<tr>
			<th></th>
			<th>Giro</th>
			<th colspan="3">{{ $data['headers']['industry'] }}</th>
			<th>País</th>
			<th colspan="2">{{ $data['headers']['country'] }}</th>
		</tr>
		<tr>
			<th></th>
			<th>Aspectos evaluados</th>
			<th colspan="9">{{ $data['headers']['aspects_evaluated'] }}</th>
		</tr>

		<tr>
			<th></th>
		</tr>

		<tr>
			<th>
			<th colspan="9">
				Requerimientos legales:
			</th>
		</tr>
		<tr>
			<th></th>
			<th>Matería</th>
			<th>Aspecto</th>
			<th>Nº de Requerimiento</th>
			<th>Requerimiento</th>
			<th>Descripción de requerimiento</th>
			<th>Fundamento Legal</th>
			<th>Condición</th>
			<th>Tipo de evidencia</th>
			<th>Documento Especifico</th>
			<th>Periodo de Actualización</th>
		</tr>
		@foreach($data['requirements'] as $record)
		<tr>
			<th></th>
			<th>{{ $record['matter'] }}</th>
			<th>{{ $record['aspect'] }}</th>
			<th>{{ $record['no_requirement'] }}</th>
			<th>{{ $record['requirement'] }}</th>
			<th>{{ $record['description'] }}</th>
			<th>
				{!! $record['legals_name'] !!}
				<a href="{{ $record['link_legal'] }}" target="_blank">
					Conoce más dando clic aquí
				</a>
			</th>
			<th>{{ $record['condition'] }}</th>
			<th>{{ $record['evidence'] }}</th>
			<th>{{ $record['document'] }}</th>
			<th>{{ $record['periodicity'] }}</th>
		</tr>
		@endforeach
	</tbody>
</table>