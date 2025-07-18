@extends('theme.master')
@section('view')
  <div id="view">
    <requirement-list :id-form="{{ intval($data['id_form']) }}"/>
  </div>
@endsection
