<script>
const NO_MADE = 11;
const MADE = 12;
var clsHandle = '';
/**
 * Open action 
 */
function openTasks(classHandle) {
    $('.loading').removeClass('d-none');
    $(classHandle).addClass('d-none');
    reloadTasks()
    setTimeout(() => {
        $('.loading').addClass('d-none');
        $('.tasks').removeClass('d-none');
    }, 1000)
    clsHandle = classHandle;
}
/**
 * Close action 
 */
function closeTasks() {
    $('.loading').removeClass('d-none');
    $('.tasks').addClass('d-none');
    if (clsHandle == '.action') { reloadActionKeepPage() } else { reloadSubActionKeepPage() }
    setTimeout(() => {
        $('.loading').addClass('d-none');
        $(clsHandle).removeClass('d-none');
    }, 1000)
}
/*
 * Show task stage
 */
function showTasks(idActionPlan, idPeriod, period, classHandle){
    document.querySelector('#idActionPlan').value = idActionPlan;
    document.querySelector('#idPeriodCurrent').value = idPeriod;
    cuurentTitleTask(idActionPlan, classHandle)
    openTasks(classHandle);
}
/**
 * Title task
 */
function cuurentTitleTask(idActionPlan, classHandle){
    if (classHandle == '.subAction') {
        console.log('sub');
        document.querySelector('#noReqTitleTaskLabel').innerHTML = 'No. Subrequerimiento';
        document.querySelector('#reqTitleTaskLabel').innerHTML = 'Subrequerimiento';
        let current = currentPageSub.data.filter( e => e.id_action_plan == idActionPlan );
        document.querySelector('#noReqTitleTask').innerHTML = current[0]['no_subrequirement'];
        document.querySelector('#reqTitleTask').innerHTML = current[0]['subrequirement'];
    }
    else { // .action
        console.log('req');
        document.querySelector('#noReqTitleTaskLabel').innerHTML = 'No. Requerimiento';
        document.querySelector('#reqTitleTaskLabel').innerHTML = 'Requerimiento';
        let current = currentPageReq.data.filter( e => e.id_action_plan == idActionPlan );
        document.querySelector('#noReqTitleTask').innerHTML = current[0]['no_requirement'];
        document.querySelector('#reqTitleTask').innerHTML = current[0]['requirement'];
    }
}
/*
 * Action table
 */
const tasksTable = $('#tasksTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/action/tasks',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.idActionPlan = document.querySelector('#idActionPlan').value
        }
    },
    columns: [
        { data: 'title', className: 'td-actions text-center', orderable : true },
        { data: 'task', className: 'td-actions text-center', orderable : true },
        { data: 'complete_name', className: 'td-actions text-center', orderable : true },
        { data: 'status', className: 'td-actions text-center', orderable : true },
        { data: 'period', className: 'td-actions text-center', orderable : false },
        { data: 'id_task', className: 'td-actions text-center', orderable : false },
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                var color = '';
                if  ( (data == 'Sin Responsable') || (data == 'Rechazado') ) {
                    color = 'danger';
                }
                else if  ( (data == 'Sin Realizar') || (data == 'Revisión') ) {
                    color = 'warning';
                }
                else if ( data == 'En Proceso'  ) {
                    color = 'info';
                }
                else if ( data == 'Completado' ) {
                    color = 'success';   
                }
                return `<span class="badge badge-${color} text-white">${data}</span>`; 
            },
            targets: 3
        },
        {
            render: (data, type, row) => {
                let btnUser = '';
                let btnCheck = '';
                let files =  (row.id_user_asigned != null) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Documentos" 
                        href="javascript:openFiles(null, null, null, ${row.id_task}, 'tasks')">
                        <i class="fa fa-file-pdf-o fa-lg"></i> 
                    </a>` : '';
                if ( (ID_PROFILE_TYPE == 1 || ID_PROFILE_TYPE == 2 || ID_PROFILE_TYPE == 3 || ID_PROFILE_TYPE == 4) && (MODIFY) ) {
                    btnUser = `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Asignar Responsable" 
                            href="javascript:asignedUserTask(${data}, '${row.title}')">
                            <i class="fa fa-user-plus la-lg"></i>
                        </a>`;
                    btnCheck = ( (USER != row.id_user_asigned) && (row.status == 'Revisión') ) ? `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Aceptar documentación" 
                            href="javascript:completeTask(${data}, '${row.title}')">
                            <i class="fa fa-check-square la-lg"></i>
                        </a>` : '';
                }
                let btnEdit = '';
                let btnDelete = '';
                if (USER == row.id_user) {
                    btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar tarea" 
                            href="javascript:editTask('${row.title}', ${row.id_task}, '${row.task}', ${row.id_period})">
                            <i class="fa fa-edit la-lg"></i>
                        </a>` : '';
                    btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar tarea" 
                            href="javascript:deleteTask(${data}, '${row.title}')">
                            <i class="fa fa-times fa-lg"></i>
                        </a>`:'';
                }
                let btnRemainder = '';
                if ( (MODIFY) && (USER == row.id_user_asigned) ) {
                        btnRemainder = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Recordatorios" 
                                href="javascript:reminderTask('${row.title}', ${row.id_task}, 0)"> <i class="fa fa-bell-o fa-lg"></i>
                            </a>`;
                }
                return files+btnCheck+btnUser+btnRemainder+btnEdit+btnDelete;
            },
            targets: 5
        }
    ],
    rowCallback: (row, data) => {
        $('input.my-switch', row).attr({
            'data-on-color':'success', 
            'data-off-color':'dark', 
            'data-on-text':'<i class="fa fa-check"></i>', 
            'data-off-text':'<i class="fa fa-times"></i>'
        }).bootstrapSwitch();
    },
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
/**
 * Reload sub action table
 */
const reloadTasks = () => { tasksTable.ajax.reload(null, false) }
const reloadTasksKeepPage = () => { tasksTable.ajax.reload() }
/**
 * Get period in requirement
 */
function getPeriodAP(selector, edit){
    $('.alert-period').addClass('d-none');
    document.querySelector(selector).disabled = false;
    $.get('/action/tasks/periods', {
        '_token': '{{ csrf_token() }}',
        idPeriod: document.querySelector('#idPeriodCurrent').value,
        idActionPlan: document.querySelector('#idActionPlan').value,
        edit: edit
    },
    (data) => {
        if (data.length > 0) {
            var html = '<option value=""></option>';
            data.forEach(element => {
                html += `<option value="${element['id_period']}">${element['period']}</option>`;
            });    
        }
        else{
            document.querySelector(selector).disabled = true;
            $('.alert-period').removeClass('d-none');
        }
        document.querySelector(selector).innerHTML = html;
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Set tasks
 */
function addTask(idActionPlan, idPeriod){
    document.querySelector('#idActionPlan').value = idActionPlan;
    document.querySelector('#idPeriodCurrent').value = idPeriod;
    $('.loading').removeClass('d-none')
    getPeriodAP('#s-idPeriod', 0);
    document.querySelector('#setTask').reset();
    $('#setTask').validate().resetForm();
    $('#setTask').find(".error").removeClass("error");
    $('#addModal').modal({backdrop:'static', keyboard: false});
    $('.loading').addClass('d-none')
}
/**
 * Handler to submit set task form 
 */
$('#setTask').submit( (event) => {
    event.preventDefault() 
    if($('#setTask').valid()) {
        showLoading('#addModal')
        //handler notificaction
        $.post('/action/tasks/set', {
            '_token': '{{ csrf_token() }}',
            idActionPlan: document.querySelector('#idActionPlan').value,
            title: document.querySelector('#s-title').value,
            task: document.querySelector('#s-task').value,
            idPeriod: document.querySelector('#s-idPeriod').value
        },
        (data) => {
            showLoading('#addModal')
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                reloadActionKeepPage();
                reloadSubActionKeepPage();

                $('#addModal').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addModal')
        });
    }
});
/**
 * Edit tasks
 */
 function editTask(title, idTask, task, idPeriod){
    $('.loading').removeClass('d-none')
    getPeriodAP('#u-idPeriod', 1);
    setTimeout(() => {
        document.querySelector('#editTask').reset();
        $('#editTask').validate().resetForm();
        $('#editTask').find(".error").removeClass("error");
        $('#editModal').modal({backdrop:'static', keyboard: false});
        document.querySelector('#titleModalTask').innerHTML = title;
        document.querySelector('#u-title').value = title;
        document.querySelector('#idTask').value = idTask;
        document.querySelector('#u-task').value = task;
        document.querySelector('#u-idPeriod').value = idPeriod;
        $('.loading').addClass('d-none')
    }, 2000)
 }
 /**
 * Handler to submit update task form 
 */
$('#editTask').submit( (event) => {
    event.preventDefault() 
    if($('#editTask').valid()) {
        showLoading('#editModal')
        //handler notificaction
        $.post('/action/tasks/update', {
            '_token': '{{ csrf_token() }}',
            idTask: document.querySelector('#idTask').value,
            title: document.querySelector('#u-title').value,
            task: document.querySelector('#u-task').value,
            idPeriod: document.querySelector('#u-idPeriod').value,
            idActionPlan: document.querySelector('#idActionPlan').value,
            idPeriodCurrent: document.querySelector('#idPeriodCurrent').value
        },
        (data) => {
            showLoading('#editModal')
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                reloadActionKeepPage();
                reloadSubActionKeepPage();

                $('#editModal').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editModal')
        });
    }
});
/*
 * Delete task
 */
function deleteTask(idTask, title) {
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
            // send to server
            $('.loading').removeClass('d-none')
            $.post('{{asset('/action/tasks/delete')}}',
            {
                '_token':'{{ csrf_token() }}',
                idTask: idTask,
                idActionPlan: document.querySelector('#idActionPlan').value
            },
            (data) => {
                $('.loading').addClass('d-none')
                toastAlert(data.msg, data.status);
                if (data.status == 'success') {
                    reloadActionKeepPage();
                    reloadSubActionKeepPage();

                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            });
        }
    });
}
/**
 * Accepted documentation
 */
function completeTask(idTask, taskTitle){

    Swal.fire({
        title: `¿Estas seguro de concluir la tarea '${taskTitle}'?`,
        text: 'Si es rechazada se eliminaran los archivos relacionados',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#87CB16',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Rechazar',
        allowOutsideClick: false,
        showCloseButton: true
    }).then((result) => {
        if (result.value) {
            // 17: Completado
            let idStatus = 17;
            setDocumentStatus(idTask, idStatus);  
        }
        else if (result.dismiss == 'cancel') {
            // 19: Rechazado
            let idStatus = 19;
            setDocumentStatus(idTask, idStatus);  
        }
    });
}
/**
 * task obout status
 */
function setDocumentStatus(idTask, idStatus){
    $('.loading').removeClass('d-none')
    $.post('/action/tasks/status', {
        '_token': '{{ csrf_token() }}',
        idActionPlan: document.querySelector('#idActionPlan').value,
        idStatus: idStatus,
        idTask: idTask
    },
    (data) => {
        $('.loading').addClass('d-none')
        toastAlert(data.msg, data.status);
        reloadActionKeepPage()

    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
        $('.loading').addClass('d-none')
    });
}

/*************** Reminder ***************/

/**
 * Show reminders close date
 */
function reminderTask(title, idTask){
    document.querySelector('#remindersCloseForm').reset();
    $('#remindersCloseForm').validate().resetForm();
    $('#remindersCloseForm').find(".error").removeClass("error");
    document.querySelector("#textClose").innerHTML = 'Fecha máxima';
    document.querySelector("#textCloseFooter").innerHTML = 'Fecha máxima';
    // Data AP
    $.get('/action/tasks/data/reminders', {
        '_token': '{{ csrf_token() }}',
        idActionPlanTask: document.querySelector('#idActionPlan').value,
        idActionPlan: null,
        idObligation: null,
        idTask: idTask,
        typeDate: 0
    },
    (data) => {
        flatpickr('#s-reminder', {
            locale: lenguageFlatpickr,
            allowInput: false,
            clickOpens: false,
            inline: true,
            position: 'below',
            monthSelectorType: 'static',
            mode: 'multiple',
            dateFormat: 'Y-m-d',
            minDate: 'today',
            maxDate: data.task.closeDate,
            defaultDate: data.dates,
            onChange: (selectedDates, dateStr, instance) => {
                setDatesReminder(null, null, idTask, selectedDates, 0);
            },
        });
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
    document.querySelector('#remindersCloseTitle').innerHTML = `Tarea: ${title}`;
    $('#remindersCloseModal').modal({backdrop:'static', keyboard: false});
}

/**
 * Actions tasks
 */
function actionsTasks(row, origin, idActionPlan){
    let btnUser = '';
    let btnCheck = '';
    let files =  (row.id_user_asigned != null) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Documentos" 
            href="javascript:openFiles(null, null, null, ${row.id_task}, '${origin}')">
            <i class="fa fa-file-pdf-o fa-lg"></i> 
        </a>` : '';
    if ( (ID_PROFILE_TYPE == 1 || ID_PROFILE_TYPE == 2 || ID_PROFILE_TYPE == 3 || ID_PROFILE_TYPE == 4) && (MODIFY) ) {
        btnUser = `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Asignar Responsable" 
                href="javascript:asignedUserTask(${row.id_task}, '${row.title}', ${idActionPlan})">
                <i class="fa fa-user-plus la-lg"></i>
            </a>`;
        btnCheck = ( (USER != row.id_user_asigned) && (row.status == 'Revisión') ) ? `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Aceptar documentación" 
                href="javascript:completeTask(${row.id_task}, '${row.title}')">
                <i class="fa fa-check-square la-lg"></i>
            </a>` : '';
    }
    let btnEdit = '';
    let btnDelete = '';
    if (USER == row.id_user) {
        btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar tarea" 
                href="javascript:editTask('${row.title}', ${row.id_task}, '${row.task}', ${row.id_period})">
                <i class="fa fa-edit la-lg"></i>
            </a>` : '';
        btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar tarea" 
                href="javascript:deleteTask(${row.id_task}, '${row.title}')">
                <i class="fa fa-times fa-lg"></i>
            </a>`:'';
    }
    let btnRemainder = '';
    if ( (MODIFY) && (USER == row.id_user_asigned) ) {
            btnRemainder = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Recordatorios" 
                    href="javascript:reminderTask('${row.title}', ${row.id_task}, 0)"> <i class="fa fa-bell-o fa-lg"></i>
                </a>`;
    }
    return files+btnCheck+btnUser+btnRemainder+btnEdit+btnDelete;
}
</script>