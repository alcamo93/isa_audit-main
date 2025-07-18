@extends('theme.master')
@section('view')
    <div id="view">
        <div class="matters">
            @include('catalogs.matters.matters')
        </div>
        <div class="aspects d-none">
            @include('catalogs.matters.aspects')
        </div>
    </div>
    @include('catalogs.matters.mattersModal')
    @include('catalogs.matters.aspectsModal')
    @include('components.loading')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('catalogs.matters.config_permissions')
@include('components.validate_js')
@include('catalogs.matters.legal_matters_js')
@include('catalogs.matters.aspects_js')
@include('components.help.helpModule')
@endsection