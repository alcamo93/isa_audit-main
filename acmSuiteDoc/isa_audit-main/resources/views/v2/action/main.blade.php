@extends('theme.master')
@section('view')
    <div id="view">
        <action-plan-list
            :id-audit-process="{{ intval($data['id_audit_processes']) }}"
            :id-aplicability-register="{{ intval($data['id_aplicability_register']) }}"
            origin="{{ $data['origin'] }}"
            :id-section-register="{{ intval($data['id_section_register']) }}"
            :id-action-register="{{ intval($data['id_action_register']) }}"
        />
    </div>
@endsection
