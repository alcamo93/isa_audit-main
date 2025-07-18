@extends('theme.master')
@section('view')
<div id="view">
  <project-dashboard-view 
    :id-audit-process="{{ intval($id_audit_process) }}"
    :id-aplicability-register="{{ intval($id_aplicability_register) }}"
  />
</div>
@endsection