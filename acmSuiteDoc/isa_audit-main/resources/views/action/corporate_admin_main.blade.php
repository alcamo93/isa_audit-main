@extends('theme.master')
@section('view')
    <div class="view action">
        @include('action.requirements.action_table')
    </div>
    <div class="view subAction sub d-none">
        @include('action.subrequirements.sub_action_table')
    </div>
    <div class="view tasks d-none">
        @include('action.tasks.tasks')
    </div>
    <div class="view comments d-none">
        @include('comments.comments')
    </div>
    <div class="view files d-none">
        @include('components.files.fileDatatable')
    </div>
    @include('action.tasks.modals')
    @include('action.config.modals')
    @include('comments.commentsModal')
    @include('comments.commentsFVModal')
    @include('components.files.fileModal')
    @include('components.reminders.main')
    @include('components.loading')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('components.validate_js')
@include('components.get_corporates_js')
@include('action.config_permissions')
@include('action.contracts.corporate_admin_js')
@include('action.requirements.action_table_js')
@include('action.subrequirements.sub_action_table_js')
@include('action.tasks.tasks_js')
@include('action.config.config_js')
@include('comments.comments_js')
@include('components.files.file_js')
@include('components.reminders.main_js')
@include('components.help.helpModule')
@endsection