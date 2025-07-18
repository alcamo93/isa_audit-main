@extends('theme.master')
@section('view')
    <div id="view">
        <div class="guidelines">
            @include('catalogs.legals.guidelines.guidelines')
        </div>
        <div class="basis d-none">
            @include('catalogs.legals.basis.basis')
        </div>
    </div>
    @include('catalogs.legals.guidelines.addModalGuidelines')
    @include('catalogs.legals.guidelines.editModalGuidelines')
    @include('catalogs.legals.basis.addModalBasis')
    @include('catalogs.legals.basis.editModalBasis')
    @include('catalogs.legals.basis.fullBasisView')
    @include('components.loading')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('catalogs.legals.config_permissions')
@include('components.validate_js')
@include('components.locations_js')
@include('catalogs.legals.legals_js')
@include('catalogs.legals.titlePage_js')
@include('components.help.helpModule')
@endsection
