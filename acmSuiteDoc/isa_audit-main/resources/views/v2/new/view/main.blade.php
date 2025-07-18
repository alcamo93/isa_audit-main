@extends('theme.master')
@section('view')
  <div id="view">
    <view-new :id-new="{{ intval($data['id_new']) }}"/>
  </div>
@endsection