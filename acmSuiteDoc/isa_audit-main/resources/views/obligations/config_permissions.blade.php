<script>
/*************** Permissions ***************/
const USER = "{{  $idUser }}";
const ORIGIN = 'comments_obligation';
const CREATE = ("{{  Session::get('create') }}" == 1) ? true : false;
const MODIFY = ("{{  Session::get('modify') }}" == 1) ? true : false;
const DELETE = ("{{  Session::get('delete') }}" == 1) ? true : false;
const TODAY = "{{ $today }}";
const ID_PROFILE_TYPE = parseInt("{{ Session::get('profile')['id_profile_type'] }}");
document.querySelector('#buttonAddObligation').style.display = (CREATE) ? 'block' : 'none';
const statusObligation = {
    0: {'color': 'secondary', 'status': 'N/A'},
    20: {'color': 'warning', 'status': 'No Iniciada'},
    21: {'color': 'primary', 'status': 'En Revisión'},
    22: {'color': 'danger', 'status': 'Vencido'},
    23: {'color': 'success', 'status': 'Aprobado'},
    24: {'color': 'danger', 'status': 'Rechazado'}
}
const currentAP = {
    idCustomer: null,
    idCorporate: null,
    idStatus: null,
    idAuditProcess: null,
    idActionRegister: null,
    idObligation: null
}
const currentFile = {
    idFile: null,
    urlFile: null,
    nameFile: null
}
const currentComment = {
    idComment: null
}
const currentDates = {
    idPeriod: null,
    renewalDate: null
}
</script>