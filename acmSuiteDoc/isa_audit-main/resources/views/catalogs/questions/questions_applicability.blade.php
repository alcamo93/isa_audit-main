@extends('theme.master')
@section('view')
    <div id="view">
        <div class="questions">
            @include('catalogs.questions.questions.questions')
        </div>
        <div class="showAnswers d-none">
            @include('catalogs.questions.answers.answers')
        </div>
        <div class="requirementsSelection d-none">
            @include('catalogs.questions.requirements.requirements_selection')
        </div>
        <div class="showSelectedRequirements d-none">
            @include('catalogs.questions.requirements.requirements_selected')
        </div>
        <div class="subrequirementsSelection d-none">
            @include('catalogs.questions.subrequirements.subrequirements_selection')
        </div>
        <div class="subrequirementsSelected d-none">
            @include('catalogs.questions.subrequirements.subrequirements_selected')
        </div>
        <div class="basisSelection d-none">
            @include('catalogs.questions.basis.basis_selection')
        </div>
        <div class="showBasis d-none">
            @include('catalogs.questions.basis.basis_selected')
        </div>
        <div class="showDependency d-none">
            @include('catalogs.questions.questions.dependency.dependency')
        </div>
    </div>
    @include('catalogs.questions.questions.addModal')
    @include('catalogs.questions.questions.fullViewModal')
    @include('catalogs.legals.basis.fullBasisView')
    @include('catalogs.requirements.requirements.full_view.modal_requirement')
    @include('catalogs.requirements.subrequirements.full_view.modal_subrequirement')
    @include('catalogs.questions.answers.modal')
    @include('components.loading')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.get_corporates_js') 
@include('components.locations_js')
@include('components.toggle.toggle_js')
@include('catalogs.questions.config_permissions')
@include('components.validate_js')
@include('catalogs.questions.questions.questions_applicability_js')
@include('catalogs.questions.questions.dependency.dependency_js')
@include('catalogs.questions.answers.answers_js')
@include('catalogs.questions.requirements.requirements_js')
@include('catalogs.requirements.requirements.full_view.modal_requirement_js')
@include('catalogs.questions.subrequirements.subrequirements_js')
@include('catalogs.requirements.subrequirements.full_view.modal_subrequirement_js')
@include('catalogs.questions.basis.basis_js')
@endsection