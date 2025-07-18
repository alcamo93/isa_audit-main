<table>
	<thead>
		<tr>
			<th colspan="22"></th>
		</tr>
		<tr>
			<th> 
			<th colspan="{{ (($risk == true) ? 8 : 7) }}">
				Reporte de hallazgos de auditoría:
			</th>
		</tr>
		<tr>
			<th> 
			<th>
				Matería
			</th>
			<th>
				Aspecto
			</th>
			<th>
				Nº de Hallazgo
			</th>
			<th>
				Requerimiento
			</th>
			<th>
				Hallazgo/Recomendación
			</th>
			<th>
				Fundamento Legal
			</th>
			<th>
				Imagenes de Hallazgos
			</th>
			@if ($risk == true)
			<th>
				Clasificación de Riesgo
			</th>
			@endif
		</tr>
	</thead>
	<tbody>
		@foreach($data as $matter)
			@foreach($matter['aspects'] as $aspect)
				@foreach($aspect['findings'] as $main)
					<tr>
						<th> 
						<th>
							{{ $main['matter'] }}
						</th>
						<th>
							{{ $main['aspect'] }}
						</th>
						<th>
							{{ $main['no_requirement'] }}
						</th>
						<th>
							{{ $main['requirement'] }}
						</th>
						<th>
							{{ $main['finding'] }}
						</th>
						<th>
							{!! $main['legals_name'] !!}
							<a href="{{ $main['link_legal'] }}" target="_blank">
								Conoce más dando clic aquí
							</a>
						</th>
						<th>
							{!! $main['name_images'] !!}<br>
							<a href="{{ $main['link_images'] }}" target="_blank">
								Conoce más dando clic aquí
							</a>
						</th>
						@if ($risk == true)
						<th>
							@foreach($main['risk'] as $mainRisk)
								{{ $mainRisk }} <br>
							@endforeach
						</th>
						@endif
					</tr>
					@foreach($main['child_findings'] as $record)
						<tr>
							<th> 
							<th>
								{{ $record['matter'] }}
							</th>
							<th>
								{{ $record['aspect'] }}
							</th>
							<th>
								{{ $record['no_requirement'] }}
							</th>
							<th>
								{{ $record['requirement'] }}
							</th>
							<th>
								{{ $record['finding'] }}
							</th>
							<th>
								{!! $record['legals_name'] !!}<br>
								<a href="{{ $record['link_legal'] }}" target="_blank">
									Conoce más dando clic aquí
								</a>
							</th>
							<th>
								{!! $record['name_images'] !!}
								<a href="{{ $record['link_images'] }}" target="_blank">
									Conoce más dando clic aquí
								</a>
							</th>
							@if ($risk == true)
							<th>
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