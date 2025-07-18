@extends('theme.master')
@section('view')
<div id="view">
    <corporate-dashboard-compliance-view 
        :id-audit-process="{{ intval($id_audit_process) }}"
        :id-aplicability-register="{{ intval($id_aplicability_register) }}"
        :id-audit-register="{{ intval($id_audit_register) }}"
    />
</div>
@endsection