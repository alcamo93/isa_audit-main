@extends('theme.master')
@section('view')
<div id="view">
    <customer-dashboard-view :id-customer="{{ intval($id_customer) }}" />
</div>
@endsection