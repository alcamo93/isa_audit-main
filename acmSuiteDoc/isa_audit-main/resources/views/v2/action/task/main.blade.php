@extends('theme.master')
@section('view')
    <div id="view">
        <task-list
            :id-audit-process="{{ intval($data['id_audit_processes']) }}"
            :id-aplicability-register="{{ intval($data['id_aplicability_register']) }}"
            origin="{{ $data['origin'] }}"
            :id-section-register="{{ intval($data['id_section_register']) }}"
            :id-action-register="{{ intval($data['id_action_register']) }}"
            :id-action-plan="{{ intval($data['id_action_plan']) }}"
        />
    </div>
@endsection
