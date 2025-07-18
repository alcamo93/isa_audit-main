<script>
$(document).ready( () => {
    setFormValidation('#setProcessesForm');
    setFormValidation('#updateProcessesForm');
    setFormValidation('#wizardForm');
    setFormValidation('#wizardSubForm');
    setFormValidation('#formFinding');
});
activeMenu(12, 'Auditoria');
const CREATE = ("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = ("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = ("{{  Session::get('delete') }}" == 1) ? true : false;
const ID_USER = "{{  $idUser }}";
const ID_PROFILE_TYPE = "{{  $idProfileType }}";
const ID_CORPORATE = "{{ $idCorporate }}";
const profilesType = {
    ownerGlobal: 1,
    ownerOperative: 2,
    corporate: 3,
    coordinator: 4,
    operative: 5
}
</script>