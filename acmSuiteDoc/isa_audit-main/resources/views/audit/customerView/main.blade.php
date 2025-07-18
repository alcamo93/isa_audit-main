@extends('theme.master')
@section('view')
    <div id="view">
        <!-- Customers registers to audit process -->
        <div class="main-register">
            
        </div>
        @include('audit.info.info')
        @include('audit.quiz.quiz')
        @include('audit.quiz.subQuiz')
        @include('components.loading')
    </div>
    @include('audit.quiz.modal')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.validate_js')
@include('components.toggle.toggle_js')
@include('components.get_corporates_js')
@include('audit.audit_js')
@include('audit.info.service_js')
@include('audit.customerView.main_js')
@include('audit.info.info_js')
@include('audit.quiz.quiz_js')
@include('audit.quiz.subQuiz_js')
@endsection