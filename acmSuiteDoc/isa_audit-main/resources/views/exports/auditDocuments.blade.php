<table>
	<thead>
		<tr>
			<th colspan="24"></th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="3"></th>
			<th colspan="15"
				style="
				font-size:40px;
				font-family:sans-serif;
				color:#003e52;
				"
			>
				Checklist de información requerida
			</th>
			<th colspan="3"></th>
		</tr>
		<tr>
			<th colspan="24"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Razón Social</th>
			<th colspan="6" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['corp_trademark'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Planta</th>
			<th colspan="6" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['corp_tradename'] }}</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Dirección</th>
			<th colspan="6" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['full_address'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Fecha de reporte</th>
			<th colspan="6" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['date'] }}</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Nombre auditoría</th>
			<th colspan="6" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['audit_processes'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Alcance</th>
			<th colspan="6" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['scope'] }}</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Auditor Lider</th>
			<th colspan="6" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['leader_auditor'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Giro</th>
			<th colspan="6" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['industry'] }}</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Auditor(es)</th>
			<th colspan="15" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['auditors'] }}</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Aspectos evaluados</th>
			<th colspan="15" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['aspects'] }}</th>
		</tr>
		<tr>
			<th colspan="24"></th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="18">
				DOCUMENTOS REQUERIDOS PARA AUDITORÍA
			</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="1">No.</th>
			<th colspan="3">Aspecto</th>
			<th colspan="3">Tipo de aplicabilidad</th>
			<th colspan="3">Requerimiento</th>
			<th colspan="8">Documento</th>
		</tr>
		@if( sizeof($data['requirements']) > 0 )
		@foreach($data['requirements'] as $key => $row)
		<tr>
			<th colspan="3"></th>
			<th colspan="1">{{ $row['index']  }}</th>
			<th colspan="3">{{ $row['aspect']  }}</th>
			<th colspan="3">{{ $row['application_type']  }}</th>
			<th colspan="3">{{ $row['no_requirement']  }}</th>
			<th colspan="8">{{ $row['document']  }}</th>
		</tr>
		@endforeach
		@else
		<tr>
			<th colspan="3"></th>
			<th colspan="15">El auditor solicitará los documentos necesarios durante la auditoria</th>
		</tr>
		@endif
	</tbody>
</table>