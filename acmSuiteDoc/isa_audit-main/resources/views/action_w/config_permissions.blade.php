<script>
$(document).ready( () => {
    setFormValidation('#setTask');
    setFormValidation('#asignedForm');
    setFormValidation('#asignedCloseDateForm');
    setFormValidation('#permitForm');
    setFormValidation('#setFile');
    setFormValidation('#formComment');
    setFormValidation('#expiredAgainForm');
    initPickerFilter();
    activeMenu(13, 'Plan de Acción');
});
/*************** Permissions ***************/
const USER = "{{ $idUser }}";
const ORIGIN = 'comments_ap';
const CREATE = ("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = ("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = ("{{  Session::get('delete') }}" == 1) ? true : false;
const TODAY = "{{ $today }}";
const ID_PROFILE_TYPE = "{{ $idProfileType }}";
const tables = {
    actions: null,
    expired: null,
    tasks: null,
    comments: null
}
const currentAR = {
    idActionRegister: null, 
    idAuditProcess: null, 
    idCustomer: null,
    idCorporate: null,
    customerTrademark: null, 
    corporateTradename: null, 
    auditProcess: null,
    scopeAudit: null,
    evaluateRisk: null
}
const currentAP = {
    idActionPlan: null,
    idRequirement: null,
    noRequirement: null,
    status: null,
    complex: null,
    section: null,
    realCloseDate: null,
    idActionExpired: null,
    blockTask: false,
    permission: null
}
const currentTask = {
    idTask: null,
    idActionPlan: null,
    idPeriodCurrent: null,
    idFile: null,
    urlFile: null,
    nameFile: null,
    idComment: null,
    permission: null
}
const statusAP = {
    0: {'color': 'secondary', 'status': 'N/A'},
    13: {'color': 'secondary', 'status': 'Sin Asignar'},
    16: {'color': 'warning', 'status': 'En Curso'},
    17: {'color': 'success', 'status': 'Completado'},
    18: {'color': 'primary', 'status': 'En Revisión'},
    25: {'color': 'danger', 'status': 'Vencido'},
    27: {'color': 'danger', 'status': 'Cerrado'}
}
const statusTask = {
    0: {'color': 'secondary', 'status': 'N/A'},
    11: {'color': 'warning', 'status': 'No Iniciada'},
    12: {'color': 'warning', 'status': 'En Curso'},
    14: {'color': 'danger', 'status': 'Vencido'},
    15: {'color': 'primary', 'status': 'En Revisión'},
    19: {'color': 'success', 'status': 'Aprobado'},
    26: {'color': 'danger', 'status': 'Rechazado'}
}
const filtersDates = {
    dateMin: null,
    dateMax: null
}
document.querySelector('#buttonAddTask').style.display = (CREATE) ? 'block' : 'none';
function disabledByPermission(permission, className){
    const fields = document.querySelectorAll(className);
    for (const field of fields) {
        field.disabled = !permission;
    }
}
const permission = {
    '1': {
        modifyTask: true,
        completeTask: true,
        setDocument: true
    },
    '2': {
        modifyTask: true,
        completeTask: true,
        setDocument: true
    },
    '3': {
        modifyTask: true,
        completeTask: true,
        setDocument: true
    },
    '4': {
        modifyTask: true,
        completeTask: true,
        setDocument: true
    },
    '5': {
        modifyTask: false,
        completeTask: false,
        setDocument: false
    }
}
</script>