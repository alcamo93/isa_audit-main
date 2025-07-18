@extends('theme.master')
@section('view')
	<div id="view">
		<account :id-user="{{ intval($data['id_user']) }}"/>
	</div>
@endsection
