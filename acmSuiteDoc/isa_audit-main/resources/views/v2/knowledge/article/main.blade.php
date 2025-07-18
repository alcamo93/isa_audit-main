@extends('theme.master')
@section('view')
  <div id="view">
    <article-list :id-guideline="{{ intval($data['id_guideline']) }}"/>
  </div>
@endsection