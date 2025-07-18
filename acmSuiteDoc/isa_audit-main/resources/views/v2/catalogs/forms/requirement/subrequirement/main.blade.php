@extends('theme.master')
@section('view')
  <div id="view">
    <subrequirement-list 
      :id-form="{{ intval($data['id_form']) }}"
      :id-requirement="{{ intval($data['id_requirement']) }}"
    />
  </div>
@endsection
