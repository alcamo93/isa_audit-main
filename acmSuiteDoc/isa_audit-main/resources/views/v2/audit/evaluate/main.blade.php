@extends('theme.master')
@section('view')
    <div id="view">
        <audit-aspect-evaluate
            :id-audit-process="{{ intval($data['id_audit_process']) }}"
            :id-aplicability-register="{{ intval($data['id_aplicability_register']) }}"
            :id-audit-register="{{ intval($data['id_audit_register']) }}"
            :id-audit-matter="{{ intval($data['id_audit_matter']) }}"
            :id-audit-aspect="{{ intval($data['id_audit_aspect']) }}"
        />
    </div>
@endsection
