@extends('theme.master')
@section('view')
    <div id="view">
        <obligation-list
            :id-audit-process="{{ intval($data['id_audit_processes']) }}"
            :id-aplicability-register="{{ intval($data['id_aplicability_register']) }}"
            :id-obligation-register="{{ intval($data['id_obligation_register']) }}"
        />
    </div>
@endsection
