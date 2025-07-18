<script>
/*************** Assigned users ***************/
const userAssigned = [
    {
        id_obligation_user: null,
        id_obligation: null,
        id_user: null,
        days: null,
        level: 1
    },
    {
        id_obligation_user: null,
        id_obligation: null,
        id_user: null,
        days: null,
        level: 2
    },
    {
        id_obligation_user: null,
        id_obligation: null,
        id_user: null,
        days: null,
        level: 3
    }
];
/**
 * Get user for selected
 */
function getUsersService(){
    return new Promise((resolve, reject) => {
        $.get('/obligations/users', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAuditProcess: currentAP.idAuditProcess
        },
        (data) => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText)
        });
    })
}

function setUsersInOption(data, selector){
    return new Promise((resolve, reject) => {
        let html = '<option selected disabled value="">Seleccione un usuario</option>';
        if (data.length > 0) {
            data.forEach(u => {
                const {first_name, second_name, last_name} = u.person;
                const completeName = `${first_name} ${second_name} ${last_name}`;
                html += `<option value="${u.id_user}">${completeName}</option>`;
            });
        }else{
            html = `<option value="">No cuenta con Usuarios</option>`;
        }
        document.querySelector(selector+'-primary').innerHTML = html;
        document.querySelector(selector+'-secondary').innerHTML = html;
        document.querySelector(selector+'-terceary').innerHTML = html;
        resolve();
    });
}

function cleanUserObject(){
    userAssigned.forEach(u => {
        u.id_obligation_user = null;
        u.id_obligation = currentAP.idObligation;
        u.id_user = null;
        u.days = null;
    });
}
/**
 * Assigned user
 */
function asignedUser(idObligation, idAuditProcess) {
    currentAP.idObligation = idObligation;
    currentAP.idAuditProcess = idAuditProcess;
    $('.loading').removeClass('d-none')
    getUsersService()
    .then(data => ( setUsersInOption(data, '#idUser') ) )
    .then(data => ( getDataObligationService() ) ) 
    .then(data => {
        console.log(data);
        document.querySelector('#asignedForm').reset();
        $('#asignedForm').validate().resetForm();
        $('#asignedForm').find(".error").removeClass("error");
        cleanUserObject();
        document.querySelector('#btnTrashPrimary').disabled = false;
        document.querySelector('#asignedTitle').innerHTML = `Asignación de Responsable: ${data.title}`;
        if (data.users.length > 0) {
            data.users.forEach(u => {
                if (u.level == 1) {
                    document.querySelector('#btnTrashPrimary').disabled = true;
                    document.querySelector('#idUser-primary').value = u.id_user;
                    document.querySelector('#days-primary').value = u.days;
                    userAssigned[0].id_obligation_user = u.id_obligation_user;
                    userAssigned[0].id_user = u.id_user;
                    userAssigned[0].days = u.days
                }
                if (u.level == 2) {
                    document.querySelector('#idUser-secondary').value = u.id_user;
                    document.querySelector('#days-secondary').value = u.days;
                    userAssigned[1].id_obligation_user = u.id_obligation_user;
                    userAssigned[1].id_user = u.id_user;
                    userAssigned[1].days = u.days
                }
                if (u.level == 3) {
                    document.querySelector('#idUser-terceary').value = u.id_user;
                    document.querySelector('#days-terceary').value = u.days;
                    userAssigned[2].id_obligation_user = u.id_obligation_user;
                    userAssigned[2].id_user = u.id_user;
                    userAssigned[2].days = u.days
                }
            });
        }
        $('.loading').addClass('d-none')
        $('#asignedModal').modal({backdrop:'static', keyboard: false});
    })
    
}
/**
 * Handler to submit assigned user
 */
$('#asignedForm').submit( (event) => {
    event.preventDefault() 
    showLoading('#asignedModal')
    const findUsers = userAssigned.filter(u => u.id_user != '' && u.id_user != null);
    $.post('/obligations/users/assigned/set', {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        users: findUsers
    },
    data => {
        showLoading('#asignedModal')
        toastAlert(data.msg, data.status);
        if (data.status == 'success') {
            reloadObligationsKeepPage();
            $('#asignedModal').modal('hide');
        }
    })
    .fail(e => {
        toastAlert(e.statusText, 'error');
        showLoading('#asignedModal')
    });
});
/**
 * Remove user assigned
 */
function removeUser(position){
    const inLevel = {
        0: 'primary',
        1: 'secondary',
        2: 'terceary'
    }
    // remove in backend
    if (userAssigned[position].id_obligation_user != null) {
        $.post('/obligations/users/assigned/remove', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idObligationUser: userAssigned[position].id_obligation_user
        },
        data => {
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                userAssigned[position].id_obligation_user = null;
                userAssigned[position].id_user = null;
                userAssigned[position].days = null;
                document.querySelector('#idUser-'+inLevel[position]).value = '';
                document.querySelector('#days-'+inLevel[position]).value = '';
            }
        })
        .fail(e => {
            toastAlert(e.statusText, 'error');
        });
    }
    else {
        userAssigned[position].id_obligation_user = null;
        userAssigned[position].id_user = null;
        userAssigned[position].days = null;
        document.querySelector('#idUser-'+inLevel[position]).value = '';
        document.querySelector('#days-'+inLevel[position]).value = '';
    }
}
</script>