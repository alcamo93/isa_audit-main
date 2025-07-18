<script>
$(document).ready( () => {
    setFormValidation('#setTask');
    setFormValidation('#editTask');
    setFormValidation('#asignedForm');
    setFormValidation('#asignedCloseDateForm');
    setFormValidation('#permitForm');
});
activeMenu(13, 'Plan de Acción');
/*************** Permissions ***************/
const USER = "{{  $idUser }}";
const ORIGIN = 'comments_ap';
const CREATE = ("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = ("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = ("{{  Session::get('delete') }}" == 1) ? true : false;
const TODAY = "{{ $today }}";

const ID_PROFILE_TYPE = "{{ $idProfileType }}";
document.querySelector('#buttonAddTask').style.display = (CREATE) ? 'block' : 'none';
document.querySelector('#buttonAddComment').style.display = (CREATE) ? 'block' : 'none';

const currentPageReq = {
    data: []
}
const currentPageSub = {
    data: []
}

/**
 * curren Audit Process
 */
const currentAudit = {
    idAuditProcess: null,
}
</script>