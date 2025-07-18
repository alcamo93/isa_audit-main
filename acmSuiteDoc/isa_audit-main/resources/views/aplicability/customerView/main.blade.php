@extends('theme.master')
@section('view')
    <div id="view">
        <!-- Customers registers to aplicability process -->
        <div class="main-register">
            
        </div>
        @include('aplicability.info.info')
        @include('aplicability.quiz.quiz')
        @include('components.loading')
    </div>
    @include('aplicability.quiz.modal')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('components.validate_js')
@include('components.get_corporates_js')
@include('aplicability.aplicability_js')
@include('aplicability.info.services_js')
@include('aplicability.customerView.main_js')
@include('aplicability.info.info_js')
@include('aplicability.quiz.quiz_js')
@endsection