<table>
	<thead>
		<tr>
			<th colspan="24"></th>
		</tr>
		<tr>
			<th colspan="4"></th>
			<th colspan="3"></th>
			<th colspan="12"
				style="
					font-size: 50px;
					font-family:sans-serif;
					color:#003e52;
				"
			>
				Reporte de Auditoría
			</th>
			<th colspan="5"></th>
		</tr>
		<tr>
			<th colspan="24"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Cliente</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['corp_tradename'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Calle</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['street'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Fecha</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['date'] }}</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Razón social</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['corp_trademark'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Colonia</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['suburb'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Nombre auditoria</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['audit_processes'] }}</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">RFC</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['rfc'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Ciudad</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['city'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Estatus</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['status'] }}</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Auditor(es)</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['auditors'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Estado</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['state'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Alcance</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['scope'] }}</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Giro</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['industry'] }}</th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">País</th>
			<th colspan="4" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['country'] }}</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="2" style="font-family:sans-serif; font-size: 12px; color:#003e52;">Aspectos evaluados</th>
			<th colspan="10" style="font-family:sans-serif; font-size: 12px; color:#a6a6a6;">{{ $data['aspects'] }}</th>
		</tr>
		<tr>
			<th colspan="24"></th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="16">
				Descripción de Auditoría:
			</th>
		</tr>
		<tr>
			<th colspan="3"></th>
			<th colspan="1">No.</th>
			<th colspan="3">Aspecto</th>
			<th colspan="3">Nº de requerimiento</th>
			<th colspan="3">Requerimiento</th>
			<th colspan="3">Descripción del Hallazgo</th>
			<th colspan="3">Respuesta</th>
		</tr>
		@foreach($data['requirements'] as $key => $row)
		<tr>
			<th colspan="3"></th>
			<th colspan="1">{{ $row['index'] }}</th>
			<th colspan="3">{{ $row['aspect']  }}</th>
			<th colspan="3">{{ $row['no_requirement']  }}</th>
			<th colspan="3">{{ $row['requirement']  }}</th>
			<th colspan="3">{{ $row['finding']  }}</th>
			<th colspan="3">{{ $row['answer'] }}</th>
		</tr>
		@endforeach
		<tr>
			<th colspan="24"></th>
		</tr>
		<tr>
			<th colspan="24"></th>
		</tr>
		<tr>
			<th colspan="24"></th>
		</tr>
		<tr>
			<th colspan="12"></th>
			<th colspan="4">Requerimientos auditados</th>
			<th colspan="4">{{ $data['total']['total_requirements_audited'] }}</th>
		</tr>
		<tr>
			<th colspan="12"></th>
			<th colspan="4">Cumple</th>
			<th colspan="4">{{ $data['total']['total_affirmative'] }}</th>
		</tr>
		<tr>
			<th colspan="12"></th>
			<th colspan="4">No cumple</th>
			<th colspan="4">{{ $data['total']['total_negative'] }}</th>
		</tr>
		<tr>
			<th colspan="12"></th>
			<th colspan="4">No aplica</th>
			<th colspan="4">{{ $data['total']['total_not_apply'] }}</th>
		</tr>
		<tr>
			<th colspan="12"></th>
			<th colspan="4">Requerimientos no auditados</th>
			<th colspan="4">{{ $data['total']['total_requirement_no_audit'] }}</th>
		</tr>
		<tr>
			<th colspan="12"></th>
			<th colspan="4">Total requerimientos</th>
			<th colspan="4">{{ $data['total']['total_requerements'] }}</th>
		</tr>
	</tbody>
</table>