@extends('theme.master')
@section('view')
    <div class="view obligations">
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
    @include('obligations.obligations.obligationsModal')
    @include('obligations.users.usersList')
    @include('components.reminders.main')
    @include('components.loading')
@endsection
@section('javascript')
@parent
@include('obligations.config_permissions')
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('components.validate_js')
@include('components.get_users_js')
@include('obligations.obligations.corporate_admin_obligations_js')
@include('obligations.users.corporate_users_js')
@include('comments.comments_js')
@include('components.files.file_js')
@include('components.reminders.main_js')
@include('components.help.helpModule')
@endsection