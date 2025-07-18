@extends('theme.master')
@section('view')
    <div id="view">
        <div class="requirements">
            @include('catalogs.requirements.requirements.requirements_table')
        </div>
        <div class="subrequirements d-none">
            @include('catalogs.requirements.subrequirements.subrequirements_table')
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
    @include('catalogs.requirements.requirements.requirementModal')
    @include('catalogs.requirements.requirements.full_view.modal_requirement')
    @include('catalogs.requirements.subrequirements.subrequirementModal')
    @include('catalogs.requirements.subrequirements.full_view.modal_subrequirement')
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
@include('catalogs.requirements.requirements.functions_requiremenst_js')
@include('catalogs.requirements.requirements.full_view.modal_requirement_js')
@include('catalogs.requirements.requirements.audit_requirements_js')
@include('catalogs.requirements.subrequirements.audit_subrequirements_js')
@include('catalogs.requirements.subrequirements.full_view.modal_subrequirement_js')
@include('catalogs.requirements.basis.basis_js')
@include('catalogs.requirements.recomendations.recomendations_js')
@include('catalogs.requirements.title_page_js')
@include('components.help.helpModule')
@endsection