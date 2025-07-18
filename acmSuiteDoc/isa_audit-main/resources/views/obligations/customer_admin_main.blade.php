@extends('theme.master')
@section('view')
    <div class="view contracts">
        @include('obligations.contracts.customer_admin_contracts')
    </div>
    <div class="view obligations d-none">
        @include('obligations.obligations.obligations')
    </div>
    <div class="view comments d-none">
        @include('comments.comments')
    </div>
    <div class="view files d-none">
        @include('components.files.fileDatatable')
    </div>
    @include('comments.commentsModal')
    @include('comments.commentsFVModal')
    @include('components.files.fileModal')
    @include('obligations.users.usersList')
    @include('obligations.obligations.obligationsModal')
    @include('components.reminders.main')
    @include('components.loading')
    @include('obligations.requirements.requirementsList')
@endsection
@section('javascript')
@parent
@include('obligations.config_permissions')
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('components.validate_js')
@include('components.get_users_js')
@include('components.get_corporates_js')
@include('obligations.obligations.obligations_js')
@include('obligations.contracts.contracts_js')
@include('obligations.users.users_js')
@include('comments.comments_js')
@include('components.files.file_js')
@include('components.reminders.main')
@include('components.help.helpModule')
@include('obligations.requirements.requirements_js')
@endsection