<script>
/*************** Assigned users ***************/
const userAssigned = [
    {
        id_action_plan: null,
        id_plan_user: null,
        id_user: null,
        days: null,
        level: 1
    },
    {
        id_action_plan: null,
        id_plan_user: null,
        id_user: null,
        days: null,
        level: 2
    },
    {
        id_action_plan: null,
        id_plan_user: null,
        id_user: null,
        days: null,
        level: 3
    }
];
/**
 * Assigned user
 */
function asignedUser(idActionPlan) {
    currentAP.idActionPlan = idActionPlan;
    $('.loading').removeClass('d-none')
    getUsersService()
    .then(data => ( setUsersInOption(data, '#idUser') ) )
    .then(data => ( getDataActionService() ) ) 
    .then(data => {
        document.querySelector('#asignedForm').reset();
        $('#asignedForm').validate().resetForm();
        $('#asignedForm').find(".error").removeClass("error");
        const name = (data.subrequirement != null) ? data.subrequirement.no_subrequirement : data.requirement.no_requirement;
        cleanUserObject('action');
        document.querySelector('#btnTrashPrimary').disabled = false;
        document.querySelector('#asignedTitle').innerHTML = `Asignación de Responsable: ${name}`;
        // control permission in form
        disabledByPermission(data.permission, '.acm-req-control');
        // set data in form
        if (data.users.length > 0) {
            data.users.forEach(u => {
                if (u.level == 1) {
                    document.querySelector('#btnTrashPrimary').disabled = true;
                    document.querySelector('#idUser-primary').value = u.id_user;
                    document.querySelector('#days-primary').value = u.days;
                    userAssigned[0].id_plan_user = u.id_plan_user;
                    userAssigned[0].id_user = u.id_user;
                    userAssigned[0].days = u.days
                }
                if (u.level == 2) {
                    document.querySelector('#idUser-secondary').value = u.id_user;
                    document.querySelector('#days-secondary').value = u.days;
                    userAssigned[1].id_plan_user = u.id_plan_user;
                    userAssigned[1].id_user = u.id_user;
                    userAssigned[1].days = u.days
                }
                if (u.level == 3) {
                    document.querySelector('#idUser-terceary').value = u.id_user;
                    document.querySelector('#days-terceary').value = u.days;
                    userAssigned[2].id_plan_user = u.id_plan_user;
                    userAssigned[2].id_user = u.id_user;
                    userAssigned[2].days = u.days
                }
            });
        }
        $('.loading').addClass('d-none')
        $('#asignedModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none')
        toastAlert('No se pueden cargar usuarios', 'error');
    })
}
/**
 * Handler to submit assigned user
 */
$('#asignedForm').submit( (event) => {
    event.preventDefault() 
    if($('#asignedForm').valid()) {
        showLoading('#asignedModal')
        const findUsers = userAssigned.filter(u => u.id_user != '' && u.id_user != null);
        $.post('/action/users/assigned/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            users: findUsers
        },
        data => {
            showLoading('#asignedModal')
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                reloadActionKeepPage();
                $('#asignedModal').modal('hide');
            }
        })
        .fail(e => {
            toastAlert(e.statusText, 'error');
            showLoading('#asignedModal')
        });
    }
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
    if (userAssigned[position].id_plan_user != null) {
        $.post('/action/users/assigned/remove', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idPlanUser: userAssigned[position].id_plan_user
        },
        data => {
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                userAssigned[position].id_plan_user = null;
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
        userAssigned[position].id_plan_user = null;
        userAssigned[position].id_user = null;
        userAssigned[position].days = null;
        document.querySelector('#idUser-'+inLevel[position]).value = '';
        document.querySelector('#days-'+inLevel[position]).value = '';
    }
}
</script>