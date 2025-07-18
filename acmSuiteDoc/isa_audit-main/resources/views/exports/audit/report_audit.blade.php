<table>
	<thead>
		<tr>
			<th colspan="22"></th>
		</tr>
		<tr>
			<th> 
			<th colspan="{{ (($risk == true) ? 10 : 9) }}" 
				style="
					font-size: 18px; 
					font-family:sans-serif; 
					color:#003e52;"
			>
				Reporte de auditoría:
			</th>
		</tr>
		<tr>
			<th> 
			<th 
				style="
					font-size: 12px;
					font-family:sans-serif; 
					color:#767171;"
			>
				Matería
			</th>
			<th 
				style="
					font-size: 12px;
					font-family:sans-serif; 
					color:#767171;"
			>
				Aspecto
			</th>
			<th 
				style="
					font-size: 12px;
					font-family:sans-serif; 
					color:#767171;"
			>
				Nº de Requerimiento
			</th>
			<th 
				style="
					font-size: 12px;
					font-family:sans-serif; 
					color:#767171;"
			>
				Requerimiento
			</th>
			<th 
				style="
					font-size: 12px;
					font-family:sans-serif; 
					color:#767171;"
			>
				Descripción
			</th>
			<th 
				style="
					font-size: 12px;
					font-family:sans-serif; 
					color:#767171;"
			>
				Evidencia
			</th>
			<th 
				style="
					font-size: 12px;
					font-family:sans-serif; 
					color:#767171;"
			>
				Fundamento Legal
			</th>
			<th 
				style="
					font-size: 12px;
					font-family:sans-serif; 
					color:#767171;"
			>
				Respuesta
			</th>
			<th 
				style="
					font-size: 12px;
					font-family:sans-serif; 
					color:#767171;"
			>
				Hallazgo/Recomendación
			</th>
			@if ($risk == true)
			<th 
				style="
					font-size: 12px;
					font-family:sans-serif; 
					color:#767171;"
			>
				Clasificación de Riesgo
			</th>
			@endif
		</tr>
	</thead>
	<tbody>
		@foreach($data as $matter)
			@foreach($matter['aspects'] as $aspect)
				@foreach($aspect['audits'] as $main)
					<tr>
						<th> 
						<th 
							style="
								font-size: 12px;
								font-family:sans-serif; 
								color:#767171;"
						>
							{{ $main['matter'] }}
						</th>
						<th 
							style="
								font-size: 12px;
								font-family:sans-serif; 
								color:#767171;"
						>
							{{ $main['aspect'] }}
						</th>
						<th 
							style="
								font-size: 12px;
								font-family:sans-serif; 
								color:#767171;"
						>
							{{ $main['no_requirement'] }}
						</th>
						<th 
							style="
								font-size: 12px;
								font-family:sans-serif; 
								color:#767171;"
						>
							{{ $main['requirement'] }}
						</th>
						<th 
							style="
								font-size: 12px;
								font-family:sans-serif; 
								color:#767171;"
						>
							{{ $main['description'] }}
						</th>
						<th 
							style="
								font-size: 12px;
								font-family:sans-serif; 
								color:#767171;"
						>
							{{ $main['evidence'] }}
						</th>
						<th 
							style="
								font-size: 12px;
								font-family:sans-serif; 
								color:#767171;"
						>
							{!! $main['legals_name'] !!}
							<a href="{{ $main['link_legal'] }}" target="_blank">
								Conoce más dando clic aquí
							</a>
						</th>
						<th 
							style="
								font-size: 12px;
								font-family:sans-serif; 
								color:#767171;"
						>
							{{ $main['answer'] }}
						</th>
						<th 
							style="
								font-size: 12px;
								font-family:sans-serif; 
								color:#767171;"
						>
							{{ $main['finding'] }}
						</th>
						@if ($risk == true)
						<th 
							style="
								font-size: 12px;
								font-family:sans-serif; 
								color:#767171;"
						>
							@foreach($main['risk'] as $mainRisk)
								{{ $mainRisk }} <br>
							@endforeach
						</th>
						@endif
					</tr>
					@foreach($main['childs'] as $record)
						<tr>
							<th> 
							<th 
								style="
									font-size: 12px;
									font-family:sans-serif; 
									color:#767171;"
							>
								{{ $record['matter'] }}
							</th>
							<th 
								style="
									font-size: 12px;
									font-family:sans-serif; 
									color:#767171;"
							>
								{{ $record['aspect'] }}
							</th>
							<th 
								style="
									font-size: 12px;
									font-family:sans-serif; 
									color:#767171;"
							>
								{{ $record['no_requirement'] }}
							</th>
							<th 
								style="
									font-size: 12px;
									font-family:sans-serif; 
									color:#767171;"
							>
								{{ $record['requirement'] }}
							</th>
							<th 
								style="
									font-size: 12px;
									font-family:sans-serif; 
									color:#767171;"
							>
								{{ $record['description'] }}
							</th>
							<th 
								style="
									font-size: 12px;
									font-family:sans-serif; 
									color:#767171;"
							>
								{{ $record['evidence'] }}
							</th>
							<th 
								style="
									font-size: 12px;
									font-family:sans-serif; 
									color:#767171;"
							>
								{!! $record['legals_name'] !!}
								<a href="{{ $record['link_legal'] }}" target="_blank">
									Conoce más dando clic aquí
								</a>
							</th>
							<th 
								style="
									font-size: 12px;
									font-family:sans-serif; 
									color:#767171;"
							>
								{{ $record['answer'] }}
							</th>
							<th 
								style="
									font-size: 12px;
									font-family:sans-serif; 
									color:#767171;"
							>
								{{ $record['finding'] }}
							</th>
							@if ($risk == true)
							<th 
								style="
									font-size: 12px;
									font-family:sans-serif; 
									color:#767171;"
							>
								@foreach($record['risk'] as $recordRisk)
									{{ $recordRisk }} <br>
								@endforeach
							</th>
							@endif
						</tr>
					@endforeach
				@endforeach
			@endforeach
		@endforeach
	</tbody>
</table>