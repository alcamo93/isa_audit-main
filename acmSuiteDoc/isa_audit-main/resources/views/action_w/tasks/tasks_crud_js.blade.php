<script>
const userAssignedTask = [
    {
        id_task: null,
        id_task_user: null,
        id_user: null,
        days: null,
        level: 1
    },
    {
        id_task: null,
        id_task_user: null,
        id_user: null,
        days: null,
        level: 2
    },
    {
        id_task: null,
        id_task_user: null,
        id_user: null,
        days: null,
        level: 3
    }
];
/**
 * Clean attributes
 */
function cleanAttributes() {
    // selectors
    const titleTask = document.querySelector('#s-title');
    const task = document.querySelector('#s-task');
    const initDate = document.querySelector('#s-initDate');
    const endDate = document.querySelector('#s-endDate');
    // avaliable
    titleTask.disabled = false;
    task.disabled = false;
    initDate.disabled = false;
    endDate.disabled = false;
    endDate.removeAttribute('min');
    endDate.removeAttribute('max');
}
/**
 * Set tasks
 */
function addTask(){
    $('.loading').removeClass('d-none')
    currentTask.idTask = null;
    cleanUserObject('task'); 
    cleanForm('#setTask');
    blockFileds(false);
    cleanAttributes();
    // set attributs in dates
    if (currentAP.section == 'expired') {
        const initDate = document.querySelector('#s-initDate');
        const endDate = document.querySelector('#s-endDate');
        initDate.min = formatDate(new Date().toISOString(), 'backend');
        endDate.max = formatDate(currentAP.realCloseDate, 'backend');
    }
    // users
    document.querySelector('#titleModalTask').innerHTML = `Nueva Tarea`;
    return getUsersService()
    .then(data => ( setUsersInOption(data, '#idUser-task') ) )
    .then(data => {
        $('.loading').addClass('d-none');
        $('#addModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert(e, 'error');
    });
}
/**
 * block fields en modal modify
 */
function blockFileds(block) {
    const inputs = document.querySelectorAll('.input-edit');
    const labels = document.querySelectorAll('.label_edit');
    inputs.forEach(e => {
        if (!block) {
            e.classList.remove('d-none');
            e.setAttribute('data-rule-required', true);
        }
        else {
            e.classList.add('d-none');
            e.setAttribute('data-rule-required', false);
        }
    });
    labels.forEach(e => {
        if (!block) e.classList.add('d-none');
        else e.classList.remove('d-none');
    });
}
/**
 * Edit tasks
 */
function editTask(title, idTask, block){
    currentTask.idTask = idTask;
    $('.loading').removeClass('d-none')
    cleanUserObject('task');
    cleanForm('#setTask');
    getUsersService()
    .then(data => ( setUsersInOption(data, '#idUser-task') ) )
    .then(data => ( getDataTaskService() ) )
    .then(data => {
        // selectors
        document.querySelector('#titleModalTask').innerHTML = `Tarea: ${title}`;
        const blockTask = (block == 1) ? true : false;
        const fields = (blockTask) ? 'u' : 's';
        const titleTask = document.querySelector(`#${fields}-title`);
        const task = document.querySelector(`#${fields}-task`);
        const initDate = document.querySelector(`#${fields}-initDate`);
        const endDate = document.querySelector(`#s-endDate`);
        currentTask.permission = data.permission;
        // set data in form
        if (!blockTask) { // task complete
            blockFileds(false);
            titleTask.value = data.title;
            task.value = data.task;
            initDate.value = formatDate(data.init_date, 'backend');
            endDate.min = formatDate(data.init_date, 'backend');
            endDate.value = formatDate(data.close_date, 'backend');
        }
        else { // task expired
            blockFileds(true);
            titleTask.innerHTML = data.title;
            task.innerHTML = data.task;
            initDate.innerHTML = formatDate(data.init_date, 'date');
            endDate.min = formatDate(new Date().toISOString(), 'backend');
            endDate.max = formatDate(data.action_expired.real_close_date, 'backend');
            const closeDate = data.task_expired.close_date || '';
            endDate.value = formatDate(closeDate, 'backend');
        }
        // set users
        if (data.users.length > 0) {
            data.users.forEach(u => {
                if (u.level == 1) {
                    document.querySelector('#btnTrashTaskPrimary').disabled = true;
                    document.querySelector('#idUser-task-primary').value = u.id_user;
                    document.querySelector('#days-task-primary').value = u.days;
                    userAssignedTask[0].id_task_user = u.id_task_user;
                    userAssignedTask[0].id_user = u.id_user;
                    userAssignedTask[0].days = u.days
                }
                if (u.level == 2) {
                    document.querySelector('#idUser-task-secondary').value = u.id_user;
                    document.querySelector('#days-task-secondary').value = u.days;
                    userAssignedTask[1].id_task_user = u.id_task_user;
                    userAssignedTask[1].id_user = u.id_user;
                    userAssignedTask[1].days = u.days
                }
                if (u.level == 3) {
                    document.querySelector('#idUser-task-terceary').value = u.id_user;
                    document.querySelector('#days-task-terceary').value = u.days;
                    userAssignedTask[2].id_task_user = u.id_task_user;
                    userAssignedTask[2].id_user = u.id_user;
                    userAssignedTask[2].days = u.days
                }
            });
        }
        // Modal
        $('.loading').addClass('d-none');
        $('#addModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert(e, 'error');
    });    
}
/**
 * Handler to submit set task form 
 */
$('#setTask').submit( (event) => {
    event.preventDefault() 
    if($('#setTask').valid()) {
        showLoading('#addModal');
        const action = (currentTask.idTask == null) ? '/action/tasks/set' : '/action/tasks/update';
        const findUsers = userAssignedTask.filter(u => u.id_user != '' && u.id_user != null);
        sendFormService(action, findUsers) 
        .then(data => {
            showLoading('#addModal')
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                reloadTasksKeepPage();
                $('#addModal').modal('hide');
            }
        })
        .catch(e => {
            showLoading('#addModal')
            toastAlert(e, 'error');
            console.error(e);
        });
    }
});
/**
 * Remove user assigned
 */
function removeUserTask(position){
    const inLevel = {
        0: 'primary',
        1: 'secondary',
        2: 'terceary'
    }
    // remove in backend
    if (userAssignedTask[position].id_task_user != null) {
        $.post('/action/tasks/users/assigned/remove', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idTaskUser: userAssignedTask[position].id_task_user
        },
        data => {
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                userAssignedTask[position].id_task_user = null;
                userAssignedTask[position].id_user = null;
                userAssignedTask[position].days = null;
                document.querySelector('#idUser-'+inLevel[position]).value = '';
                document.querySelector('#days-'+inLevel[position]).value = '';
            }
        })
        .fail(e => {
            toastAlert(e.statusText, 'error');
        });
    }
    else {
        userAssignedTask[position].id_task_user = null;
        userAssignedTask[position].id_user = null;
        userAssignedTask[position].days = null;
        document.querySelector('#idUser-'+inLevel[position]).value = '';
        document.querySelector('#days-'+inLevel[position]).value = '';
    }
}
/**
 * allow dates
 */
$('#s-initDate').on('change', (e) => {
    limitEndDateTask();
});
$('#s-initDate').on('keyup', (e) => {
    limitEndDateTask();
});
/**
 * Dates limit task
 */
function limitEndDateTask() {
    const inputInitDate = document.querySelector('#s-initDate');
    const inputEndDate = document.querySelector('#s-endDate');
    inputEndDate.min = inputInitDate.value;
    if ((inputInitDate.value > inputEndDate.value) && (inputEndDate.value != '')) {
        toastAlert('La fecha de cierre debe ser mayor a la fecha de inicio', 'info');
        inputEndDate.value = '';
    }
}
/*
 * Delete task
 */
function deleteTask(idTask, title) {
    currentTask.idTask = idTask;
    Swal.fire({
        title: `¿Estas seguro de eliminar la tarea: '${title}'?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $('.loading').removeClass('d-none')
            deleteTaskService()
            .then(data => {
                $('.loading').addClass('d-none')
                toastAlert(data.msg, data.status);
                if (data.status == 'success') {
                    reloadTasksKeepPage();
                }
            })
            .catch(e => {
                $('.loading').addClass('d-none')
                toastAlert(e, 'error');
            });
        }
    });
}
/**
 * show data task
 */
function showTask(idTask) {
    currentTask.idTask = idTask;
    $('.loading').removeClass('d-none');
    getDataTaskService()
    .then(data => {
        const {title, task, init_date, close_date, users} = data;
        document.querySelector('#titleShowModalTask').innerHTML = `Tarea: ${title}`;
        document.querySelector('#w-title').innerHTML = title;
        document.querySelector('#w-task').innerHTML = task;
        document.querySelector('#w-initDate').innerHTML = formatDate(init_date, 'date');
        document.querySelector('#w-endDate').innerHTML = formatDate(close_date, 'date');
        let tbody = '';
        users.forEach(e => {
            const { first_name, second_name, last_name } = e.user.person;
            tbody += `
            <tr>
                <td class="text-center">${ (e.level === 1) ? 'Principal' : 'Secundario' }</td>
                <td class="text-center">${ first_name } ${ second_name} ${ last_name }</td>
            <tr>`
        }); 
        const btnDownload = document.querySelector('#downloadFileShow');
        const sectionDownload = document.querySelector('#sectionDownload');
        if (data.file != null) {
            currentTask.idFile = data.file.id_file;
            btnDownload.style.display = 'block';
            sectionDownload.classList.remove('d-none');
        }
        else {
            btnDownload.style.display = 'none';
            sectionDownload.classList.add('d-none');
        }
        $('#tableUserTask tbody').find('tr').remove();
        $('#tableUserTask tbody').append(tbody);
        $('.loading').addClass('d-none');
        $('#showModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none')
        toastAlert(e, 'error');
    });
}
document.querySelector('#downloadFileShow').addEventListener('click', function (e) {
    window.open(`/files/download/${currentTask.idFile}`);
});
</script>