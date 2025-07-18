@extends('theme.master')
@section('view')
    <div id="view">
        <applicability-aspect-list
            :id-audit-process="{{ intval($data['id_audit_processes']) }}"
            :id-aplicability-register="{{ intval($data['id_aplicability_register']) }}"
        />
    </div>
@endsection
