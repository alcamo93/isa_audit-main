<script>
/**
 * Open action 
 */
function openAction() {
    $('.loading').removeClass('d-none');
    $('.contracts').addClass('d-none');
    reloadAction();
    $('body').addClass('sidebar-mini');
    setTimeout(() => {
        $('.loading').addClass('d-none');
        $('.action').removeClass('d-none');
    }, 1000)
}
/**
 * Close action 
 */
function closeAction() {
    $('.loading').removeClass('d-none');
    $('.action').addClass('d-none');
    $('body').removeClass('sidebar-mini');
    currentPageReq.data = [];
    setTimeout(() => {
        $('.loading').addClass('d-none');
        $('.contracts').removeClass('d-none');
    }, 1000)
}
/*
 * show all requirements
 */
function showActionPlan(idActionRegister, idAuditProcess, customer, corporate){
    currentAudit.idAuditProcess = idAuditProcess;
    currentAudit.idActionRegister = idActionRegister;
    document.querySelector('#idActionRegister').value = idActionRegister;
    // Main action 
    document.querySelector('#customerTitle').innerHTML = customer;
    document.querySelector('#corporateTitle').innerHTML = corporate;
    setMatters(idActionRegister);
    openAction();
    // Sub action
    // document.querySelector('#subCustomerTitle').innerHTML = customer;
    // document.querySelector('#subCorporateTitle').innerHTML = corporate;
    // Tasks
    // document.querySelector('#taskCustomerTitle').innerHTML = customer;
    // document.querySelector('#taskCorporateTitle').innerHTML = corporate;
}
/*
 * Set mmaters in select option
 */
function setMatters(idActionRegister){
    $.get('/action/matters', {
        '_token': '{{ csrf_token() }}',
        idActionRegister: idActionRegister
    },
    (data) => {
        let options = '<option value="0">Todos</option>';
        data.forEach(element => {
                options += `<option value="${element.id_matter}">${element.matter}</option>`; 
        });
        document.querySelector('#filterIdMatter').innerHTML = options;
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Set aspects in select option
 */
function setAspects(idMatter, selectorAspect, callback){
    if (idMatter > 0) {
        $.get('/action/aspects', {
            '_token': '{{ csrf_token() }}',
            idMatter: idMatter
        },
        (data) => {
            var html = '<option value="0">Todos</option>';
            data.forEach(element => {
                html += `<option value="${element.id_aspect}">${element.aspect}</option>`; 
            });
            document.querySelector(selectorAspect).innerHTML = html;
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
        });  
    } else {
        var html = `<option value="0">Todos</option>`;
        document.querySelector(selectorAspect).innerHTML = html;
    }
    if ( callback != 'undefined' && typeof callback === 'function' ) {
        callback();
    }
}
/*
 * Action table
 */
const actionTable = $('#actionTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/action/action-plan',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.idMatter = document.querySelector('#filterIdMatter').value,
            data.idAspect = document.querySelector('#filterIdAspect').value,
            data.idActionRegister = document.querySelector('#idActionRegister').value
        }
    },
    columns: [
        { data: null, className: 'align-middle', class: 'details-control', defaultContent: '', orderable: false },
        { data: 'no_requirement', className: 'td-actions text-center', orderable : true },
        { data: 'aspect', className: 'td-actions text-center', orderable : true },
        { data: 'requirement', className: 'td-actions text-justify', width:150, orderable : true },
        { data: 'condition', className: 'td-actions text-center', orderable : true },
        { data: 'finding', className: 'td-actions text-center', width:150, orderable : false },
        { data: 'init_date', className: 'td-actions text-center', orderable : true },
        { data: 'real_close_date', className: 'td-actions text-center', orderable : true },
        { data: 'status', className: 'td-actions text-center', orderable : true },
        { data: 'complete_name', className: 'td-actions text-center', orderable : false },
        { data: 'id_action_register', className: 'td-actions text-center', orderable : false },
    ],
    columnDefs: [
        // {
        //     render: (data, type, row) => {
        //         currentPageReq.data.push(row);
        //         let btnRequirement = (row.requirement != null) ? `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Descripción de Requerimiento" 
        //                 href="javascript:showText('${row.no_requirement}', '${row.requirement}', 0)"><i class="fa fa-align-left fa-lg"></i>
        //             </a>` : '';
        //         return btnRequirement;
        //     },
        //     targets: 2
        // },

        // {
        //     render: (data, type, row) => {
        //         var recomendation = '';
        //         if (row.recomendation != null) {
        //             recomendation = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar Recomendación" 
        //                 href="javascript:showText('${row.no_requirement}', '${row.recomendation}', 0)"><i class="fa fa-list-ul fa-lg"></i>
        //             </a>`;   
        //         }else{
        //             recomendation = '<span class="badge badge-info text-white">Sin recomendación</span>';
        //         }
        //         return recomendation;
        //     },
        //     targets: 4
        // },
        // {
        //     render: (data, type, row) => {
        //         var finding = '';
        //         if (row.finding != null) {
        //             finding = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar Hallazgo" 
        //                 href="javascript:showText('${row.no_requirement}', '${row.finding}', 0)"><i class="fa fa-list-ul fa-lg"></i>
        //             </a>`;   
        //         }else{
        //             finding = '<span class="badge badge-info text-white">Sin Hallazgo</span>';
        //         }
        //         return finding;
        //     },
        //     targets: 5
        // },
        {
            render: (data, type, row) => {
                var color = '';
                if  ( (data == 'Sin Responsable') || (data == 'Sin Tareas') || (data == 'Rechazado') ) {
                    color = 'danger';
                }
                else if  ( (data == 'Sin Realizar') || (data == 'Revisión') ) {
                    color = 'warning';
                }
                else if ( data == 'En Proceso' ) {
                    color = 'info';
                }
                else if ( data == 'Completado' ) {
                    color = 'success';   
                }
                return `<span class="badge badge-${color} text-white">${data}</span>`; 
            },
            targets: 8
        },
        {
            render: (data, type, row) => {
                let name = row.complete_name;
                let picture = row.picture;
                let img = '';
                if (row.id_user_asigned) {
                    img = `<div>
                            <img width="50px" class="avatar" src="{{ asset('/assets/img/faces/') }}/${picture}">
                        </div>
                        <div>
                            <span>${name}</span>
                        </div>`
                }
                return img;
            },
            targets: 9
        },
        {
            render: (data, type, row) => {
                let btnSubrequirements = (row.has_subrequirement == 1 && row.id_user_asigned != null) ? `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Subrequerimientos" 
                            href="javascript:showSubrequirements(${row.id_action_plan}, ${row.id_requirement}, '${row.no_requirement}', '${row.requirement}')">
                            <i class="fa fa-outdent la-lg"></i>
                        </a>` : '';
                let btnTasks = (row.complex == 1) ? `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Tareas" 
                            href="javascript:showTasks(${row.id_action_plan}, ${row.id_obtaining_period}, '${row.period}', '.action')">
                            <i class="fa fa-check-square-o la-lg"></i>
                        </a>` : '';
                let btnComments = '';
                let files = '';
                let btnUser = '';
                let btnCheck = '';
                let permitAction = '';
                let btnDateClose = '';
                let btnRequest = '';
                let btnRemainder = '';
                if ( MODIFY ) {
                    if (USER == row.id_user_asigned || USER == row.id_user) {
                        btnComments = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar Comentarios" 
                                href="javascript:openComments(${row.id_action_plan}, null, 'view-ap')"><i class="fa fa-comments-o fa-lg"></i>
                            </a>`;
                        files = (row.complex == 0 && row.id_user_asigned != null && row.has_subrequirement != 1) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Documentos" 
                                href="javascript:openFiles(null, ${row.id_action_plan}, null, null, 'action_plan')">
                                <i class="fa fa-file-pdf-o fa-lg"></i>
                            </a>`: '';
                    }
                    if (ID_PROFILE_TYPE == 1 || ID_PROFILE_TYPE == 2 || ID_PROFILE_TYPE == 3 || ID_PROFILE_TYPE == 4) {
                        btnUser = `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Asignar Responsable" 
                                href="javascript:asignedUser(${row.id_action_plan}, ${row.has_subrequirement}, '${row.no_requirement}', 'action')">
                                <i class="fa fa-user-plus la-lg"></i>
                            </a>`;
                        btnCheck = ( USER != row.id_user_asigned && row.status == 'Revisión' && row.has_subrequirement == 0 && row.complex == 0) ? `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Aceptar documentación"
                                href="javascript:completeAP(${row.id_action_plan}, '${row.no_requirement}')">
                                <i class="fa fa-check-square la-lg"></i>
                            </a>` : '';   
                    }
                    if ( row.id_user_asigned != null ) {
                        if ( (USER == row.id_user) && (row.permit == 2) ) {
                            btnRequest = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Habilitar Fecha de Cierre Real" 
                                href="javascript:setPermitAdmin(${row.id_action_plan}, '${row.no_requirement}', ${row.permit}, '${row.complete_name}')"> <i class="fa fa-calendar-plus-o fa-lg"></i>
                            </a>`;  
                        }
                        else if ( USER == row.id_user_asigned ) {
                            if ( ((row.real_close_date == null ) && (TODAY > row.close_date)) && (row.permit == 0) ){
                                btnRequest = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Solicitar Fecha de Cierre Real" 
                                        href="javascript:requestPermit(${row.id_action_plan}, '${row.no_requirement}')"> <i class="fa fa-calendar-plus-o fa-lg"></i>
                                    </a>`;
                            }
                            btnDateClose = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Fecha de cierre real" 
                                href="javascript:asignedDate(${row.id_action_plan}, '${row.no_requirement}')"> <i class="fa fa-calendar-check-o fa-lg"></i>
                            </a>`;
                            if ( (row.close_date != null) && (TODAY <= row.close_date) ) {
                                btnRemainder = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Recordatorios" 
                                        href="javascript:reminderCloseDate('${row.no_requirement}', ${row.id_action_plan}, 0)"> <i class="fa fa-bell-o fa-lg"></i>
                                    </a>`;
                            }
                            if ( (row.real_close_date != null) && (TODAY <= row.real_close_date)) {
                                btnRemainder = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Recordatorios" 
                                        href="javascript:reminderRealCloseDate('${row.no_requirement}', ${row.id_action_plan}, 0)"> <i class="fa fa-bell-o fa-lg"></i>
                                    </a>`;
                            }
                        }
                    }
                }
                return btnSubrequirements+btnComments+btnTasks+files+btnCheck+btnDateClose+btnRequest+btnRemainder+btnUser;
            },
            targets: 10
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
// Add event listener for opening and closing details
$('#actionTable tbody').on('click', 'td.details-control', function () {
    let tr = $(this).closest('tr');
    let row = actionTable.row( tr );
    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        // Open this row
        row.child( subTableTasks(row.data(), row.data().id_action_plan, row.data().id_obtaining_period) ).show();
        tr.addClass('shown');
    }
});
/* Formatting function for row details - modify as you need */
function subTableTasks(requirement, idActionPlan, idPeriod) {
    // `requirement` is the original data object for the row
    let t = `
        <button type="button" id="buttonAddTask" class="btn btn-success float-right" data-toggle="tooltip" 
            title="Agregar tarea" onclick="addTask(${idActionPlan}, ${idPeriod});">
            Agregar tarea
        </button>`;
    if (requirement.tasks.length != 0) {
        t += `
        <table class="table table-bordered" cellspacing="0" width="100%">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center">Identificador</th>
                    <th class="text-center">Tarea</th>
                    <th class="text-center">Responsable</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Periodo</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>`;
        requirement.tasks.forEach(task => {
            t += `
            <tr>
                <td class="text-center">${ (task.title === null) ? '' : task.title }</td>
                <td class="text-center">${ (task.task === null) ? '' : task.task }</td>
                <td class="text-center">`;
                if (task.id_user_asigned != null) {
                    t += `<div>
                        <img width="50px" class="avatar" src="{{ asset('/assets/img/faces/') }}/${task.picture}">
                    </div>
                    <div>
                        <span>${task.complete_name}</span>
                    </div>`
                }
                else {
                    t += '';    
                }
                t += `</td>
                <td class="text-center">${ (task.status === null) ? '' : task.status }</td>
                <td class="text-center">${ (task.period === null) ? '' : task.period }</td>
                <td class="text-center">
                    ${actionsTasks(task, 'action_plan', idActionPlan)}
                </td>
            </tr>`;
        });
        t += `</tbody>
        </table>`;
    }else{
        t += `<table class="table table-bordered" cellspacing="0" width="100%">
            <td class="text-center">Requerimiento Sin Tareas</td>
        </table>`;
    }
    return t;
}
/**
 * Reload action table
 */
const reloadAction = () => { actionTable.ajax.reload() }
const reloadActionKeepPage = () => { actionTable.ajax.reload(null, false) }
/**
 * Accepted documentation
 */
function completeAP(idActionPlan, noRequirement){
    Swal.fire({
        title: `¿Estas seguro de concluir el requerimiento '${noRequirement}'?`,
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
            setDocumentAPStatus(idActionPlan, idStatus);  
        }
        else if (result.dismiss == 'cancel') {
            // 19: Rechazado
            let idStatus = 19;
            setDocumentAPStatus(idActionPlan, idStatus);  
        }
    });
}
/**
 * Action obout status
 */
function setDocumentAPStatus(idActionPlan, idStatus){
    $('.loading').removeClass('d-none')
    $.post('/action/requirement/complete',
    {
        '_token':'{{ csrf_token() }}',
        idActionPlan: idActionPlan,
        idStatus: idStatus
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
        $('.loading').addClass('d-none');
    });
}
</script>