<script>
/**
 * Get user for selected
 */
function getUsersService(){
    return new Promise((resolve, reject) => {
        $.get('/action/users', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idActionRegister: currentAR.idActionRegister
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
function cleanUserObject(type){
    if (type == 'action') {
        userAssigned.forEach(u => {
            u.id_action_plan = currentAP.idActionPlan;
            u.id_plan_user = null;
            u.id_user = null;
            u.days = null;
        });
    }
    else {
        userAssignedTask.forEach(u => {
            u.id_task = currentTask.idTask;
            u.id_task_user = null;
            u.id_user = null;
            u.days = null;
        });
    }
}
</script>