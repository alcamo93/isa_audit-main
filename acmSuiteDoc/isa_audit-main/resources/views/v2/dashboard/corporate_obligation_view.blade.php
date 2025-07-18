@extends('theme.master')
@section('view')
<div id="view">
    <corporate-dashboard-obligation-view 
        :id-audit-process="{{ intval($id_audit_process) }}" 
        :id-aplicability-register="{{ intval($id_aplicability_register) }}"
        :obligation-register-id="{{ intval($obligation_register_id) }}"
    />
</div>
@endsection