@extends('theme.master')
@section('view')
    <div id="view">
        <applicability-aspect-evaluate
            :id-audit-process="{{ intval($data['id_audit_process']) }}"
            :id-aplicability-register="{{ intval($data['id_aplicability_register']) }}"
            :id-contract-matter="{{ intval($data['id_contract_matter']) }}"
            :id-contract-aspect="{{ intval($data['id_contract_aspect']) }}"
        />
    </div>
@endsection
