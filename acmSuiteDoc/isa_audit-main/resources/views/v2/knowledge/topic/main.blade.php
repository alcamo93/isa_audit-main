@extends('theme.master')
@section('view')
  <div id="view">
    <guideline-topic-list :id-topic="{{ intval($data['id_topic']) }}"/>
  </div>
@endsection