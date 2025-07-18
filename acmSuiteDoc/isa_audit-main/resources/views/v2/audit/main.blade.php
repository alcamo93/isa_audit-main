@extends('theme.master')
@section('view')
    <div id="view">
        <audit-aspect-list
            :id-audit-process="{{ intval($data['id_audit_processes']) }}"
            :id-aplicability-register="{{ intval($data['id_aplicability_register']) }}"
            :id-audit-register="{{ intval($data['id_audit_register']) }}"
        />
    </div>
@endsection
