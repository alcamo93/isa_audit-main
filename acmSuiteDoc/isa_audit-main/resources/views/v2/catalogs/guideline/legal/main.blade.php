@extends('theme.master')
@section('view')
  <div id="view">
    <legal-basi-list :id-guideline="{{ intval($data['id_guideline']) }}"/>
  </div>
@endsection
