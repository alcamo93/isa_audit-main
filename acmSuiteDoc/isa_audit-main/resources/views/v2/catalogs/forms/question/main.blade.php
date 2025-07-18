@extends('theme.master')
@section('view')
  <div id="view">
    <question-list :id-form="{{ intval($data['id_form']) }}"/>
  </div>
@endsection
