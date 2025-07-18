@extends('theme.master')
@section('view')
    <div id="view">
        <div class="categories-section">
        @include('catalogs.risks.categories.categories')
        </div>
        <div class="help-section d-none">
            @include('catalogs.risks.help.help')
        </div>
        
    </div>
    @include('catalogs.risks.categories.modals')
    @include('catalogs.risks.help.modals')
    @include('components.loading')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('catalogs.risks.config_permissions')
@include('components.validate_js')
@include('catalogs.risks.categories.categories_js')
@include('catalogs.risks.help.help_js')
@include('components.help.helpModule')
@endsection