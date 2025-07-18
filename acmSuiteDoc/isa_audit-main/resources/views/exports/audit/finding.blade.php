<table>
	<thead>
		<tr>
			<th colspan="22"></th>
		</tr>
		<tr>
			<th colspan="4"></th>
			<th colspan="3"></th>
			<th colspan="5" 
				style="
					font-size: 50px; 
					font-family: sans-serif; 
					color: #003e52;"
			>
				Hallazgos
			</th>
			<th colspan="4"></th>
		</tr>
		<tr>
			<th colspan="22"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th colspan="22"></th>
		</tr>
		@foreach($data as $matter)
		<tr>
			<th colspan="6"></th>
			<th colspan="4" 
				style="
					font-size: 18px; 
					font-family: sans-serif; 
					font-weight: bold; 
					color: {{ $matter['color'] }}"
			>
				{{ $matter['matter'] }}
			</th>
			<th colspan="6"></th>
		</tr>
		@foreach($matter['aspects'] as $aspect)
		<tr>
			<th colspan="2"></th>
			<th colspan="6" 
				style="
					font-size: 12px; 
					font-family: sans-serif; 
					color: #767171;"
			>
				{{ $aspect['aspect'] }}
			</th>
			<th colspan="6" 
				style="
					font-size: 12px; 
					font-family: sans-serif; 
					color: #767171;"
			>
				{{ $aspect['count'] }}
			</th>
			<th colspan="2"></th>
		</tr>
		@endforeach
		<tr>
			<th colspan="2"></th>
			<th colspan="6" 
				style="
					font-size: 12px; 
					font-family: sans-serif; 
					color: #767171;"
			>
				Total
			</th>
			<th colspan="6" 
				style="
					font-size: 12px; 
					font-family: sans-serif; 
					font-weight: bold; color:{{ $matter['color'] }};"
			>
				{{ $matter['count'] }}
			</th>
			<th colspan="2"></th>
		</tr>
		@endforeach
	</tbody>
</table>