@extends('theme.master')
@section('view')
    <div id="view">
        <div class="requirements">
            @include('requirements.requirements.requirements_table')
        </div>
        <div class="basisSelection d-none">
            @include('catalogs.requirements.basis.basis_selection')
        </div>
        <div class="showBasis d-none">
            @include('catalogs.requirements.basis.basis_selected')
        </div>
        <div class="showBasisSr d-none">
            @include('catalogs.requirements.basis.sr_basis_selected')
        </div>
    </div>
    @include('requirements.requirements.requirementModal')
    @include('requirements.requirements.full_view.modal_requirement')
    @include('catalogs.requirements.basis.fullBasisView')
    @include('catalogs.requirements.recomendations.recomendations')
    @include('components.loading')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.locations_js')
@include('components.get_corporates_js')
@include('components.toggle.toggle_js')
@include('catalogs.requirements.config_permissions')
@include('components.validate_js')
@include('requirements.requirements.functions_requiremenst_js')
@include('requirements.requirements.full_view.modal_requirement_js')
@include('catalogs.requirements.requirements.audit_requirements_js')
@include('catalogs.requirements.basis.basis_js')
@include('catalogs.requirements.recomendations.recomendations_js')
@include('catalogs.requirements.title_page_js')
@include('components.help.helpModule')
@endsection