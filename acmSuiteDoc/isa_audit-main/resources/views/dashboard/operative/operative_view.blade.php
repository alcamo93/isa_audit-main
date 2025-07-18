@extends('theme.master')
@section('view')
    @include('dashboard.updates')
@endsection
@section('javascript')
@parent
@include('components.components_js')
<script>
  activeMenu(1, 'Tablero de control');
</script>
@endsection