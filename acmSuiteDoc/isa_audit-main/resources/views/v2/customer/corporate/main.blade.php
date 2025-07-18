@extends('theme.master')
@section('view')
	<div id="view">
		<corporate-list :id-customer="{{ intval($data['id_customer']) }}"/>
	</div>
@endsection
