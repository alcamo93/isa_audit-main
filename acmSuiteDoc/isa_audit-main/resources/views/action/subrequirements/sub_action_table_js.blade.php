<script>
/**
 * Open subaction 
 */
function openSubAction() {
    $('.loading').removeClass('d-none');
    $('.action').addClass('d-none');
    reloadSubAction()
    setTimeout(() => {
        $('.loading').addClass('d-none');
        $('.subAction').removeClass('d-none');
        $('.subAction').removeClass('sub')
    }, 1000)
}
/**
 * Close subAction 
 */
function closeSubAction() {
    $('.loading').removeClass('d-none');
    $('.subAction').addClass('d-none');
    currentPageSub.data = [];
    reloadActionKeepPage();
    setTimeout(() => {
        $('.loading').addClass('d-none');
        $('.action').removeClass('d-none');
    }, 1000)
}
/*
 * show sub action plan
 */
function showSubrequirements(idActionPlan, idRequirement, noRequirement, reqContext){
    document.querySelector('#idActionPlan').value = idActionPlan;
    document.querySelector('#idRequirement').value = idRequirement;
    // req or sub req
    document.querySelector('#noReqTitle').innerHTML = noRequirement;
    document.querySelector('#reqTitle').innerHTML = reqContext;
    // tasks
    document.querySelector('#noReqTitleTask').innerHTML = noRequirement;
    document.querySelector('#reqTitleTask').innerHTML = reqContext;
    openSubAction();
}
/*
 * Action table
 */
const subActionTable = $('#subActionTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/action/action-plan/sub',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.idActionRegister = document.querySelector('#idActionRegister').value,
            data.idRequirement = document.querySelector('#idRequirement').value
        }
    },
    columns: [
        { data: null, className: 'align-middle', class: 'details-control', defaultContent: '', orderable: false },
        { data: 'no_subrequirement', className: 'td-actions text-center', orderable : true },
        { data: 'aspect', className: 'td-actions text-center', orderable : true },
        { data: 'subrequirement', className: 'td-actions text-justify', width:150, orderable : true },
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
        //         currentPageSub.data.push(row);
        //         let btnSubrequirement = (row.subrequirement != null) ? `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Descripción de Subrequerimiento" 
        //                 href="javascript:showText('${row.no_subrequirement}', '${row.subrequirement}', 1)"><i class="fa fa-align-left fa-lg"></i>
        //             </a>` : '';
        //         return btnSubrequirement;
        //     },
        //     targets: 2
        // },
        // {
        //     render: (data, type, row) => {
        //         var btnRecomendation = '';
        //         if (row.recomendation != null) {
        //             btnRecomendation = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar Recomendación" 
        //                 href="javascript:showText('${row.no_subrequirement}', '${row.recomendation}', 1)"><i class="fa fa-list-ul fa-lg"></i>
        //             </a>`;
        //         }else{
        //             btnRecomendation = '<span class="badge badge-info text-white">Sin Recomendacion</span>';
        //         }
        //         return btnRecomendation;
        //     },
        //     targets: 4
        // },
        // {
        //     render: (data, type, row) => {
        //         var finding = '';
        //         if (row.finding != null) {
        //             finding = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar Hallazgo" 
        //                 href="javascript:showText('${row.no_subrequirement}', '${row.finding}', 0)"><i class="fa fa-list-ul fa-lg"></i>
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
                let btnTasks = (row.complex == 1) ? `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Tareas" 
                        href="javascript:showTasks(${row.id_action_plan}, ${row.id_obtaining_period}, '${row.period}', '.subAction')">
                        <i class="fa fa-check-square-o la-lg"></i>
                    </a>` : '';
                let files = '';
                let btnComments = '';
                let btnUser = '';
                let btnCheck = '';
                let btnDateClose = '';
                let btnRequest = '';
                let permitAction = '';
                let btnRemainder = '';
                if ( MODIFY ) {
                    if (USER == row.id_user_asigned || USER == row.id_user) {
                        files = (row.complex == 0 && row.id_user_asigned != null) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Documentos" 
                                href="javascript:openFiles(null, ${row.id_action_plan}, null, null, 'sub_action_plan')">
                                <i class="fa fa-file-pdf-o fa-lg"></i>
                            </a>`: '';
                        btnComments = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar " 
                                href="javascript:openComments(${row.id_action_plan}, null, 'view-sub-ap')"><i class="fa fa-comments-o fa-lg"></i>
                            </a>`;
                    }
                    if (ID_PROFILE_TYPE == 1 || ID_PROFILE_TYPE == 2 || ID_PROFILE_TYPE == 3 || ID_PROFILE_TYPE == 4) {
                        btnUser = `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Asignar Responsable" 
                                href="javascript:asignedUser(${row.id_action_plan}, 0, '${row.no_subrequirement}', 'subaction')">
                                <i class="fa fa-user-plus la-lg"></i>
                            </a>`;
                        btnCheck = (row.status == 'Revisión' && row.complex == 0) ? `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Aceptar documentación" 
                                href="javascript:completeAP(${row.id_action_plan}, '${row.no_subrequirement}')">
                                <i class="fa fa-check-square la-lg"></i>
                            </a>` : '';
                    }
                    if ( row.id_user_asigned != null ) {
                        if ( (USER == row.id_user) && (row.permit == 2) ) {
                            btnRequest = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Habilitar Fecha de Cierre Real" 
                                href="javascript:setPermitAdmin(${row.id_action_plan}, '${row.no_subrequirement}', ${row.permit}, '${row.complete_name}')"> <i class="fa fa-calendar-plus-o fa-lg"></i>
                            </a>`;  
                        }
                        else if ( USER == row.id_user_asigned ) {
                            if ( ((row.real_close_date == null ) && (TODAY > row.close_date)) && (row.permit == 0) ){
                                btnRequest = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Solicitar Fecha de Cierre Real" 
                                        href="javascript:requestPermit(${row.id_action_plan}, '${row.no_subrequirement}')"> <i class="fa fa-calendar-plus-o fa-lg"></i>
                                    </a>`;
                            }
                            btnDateClose = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Fecha de cierre real" 
                                href="javascript:asignedDate(${row.id_action_plan}, '${row.no_subrequirement}')"> <i class="fa fa-calendar-check-o fa-lg"></i>
                            </a>`;
                            if ( (row.close_date != null) && (TODAY <= row.close_date) ) {
                                btnRemainder = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Recordatorios" 
                                        href="javascript:reminderCloseDate('${row.no_subrequirement}', ${row.id_action_plan}, 1)"> <i class="fa fa-bell-o fa-lg"></i>
                                    </a>`;
                            }
                            if ( (row.real_close_date != null) && (TODAY <= row.real_close_date)) {
                                btnRemainder = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Recordatorios" 
                                        href="javascript:reminderRealCloseDate('${row.no_subrequirement}', ${row.id_action_plan}, 1)"> <i class="fa fa-bell-o fa-lg"></i>
                                    </a>`;
                            }
                        }
                    }
                }
                return btnComments+btnTasks+files+btnCheck+btnDateClose+btnRequest+btnRemainder+btnUser;
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
$('#subActionTable tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = subActionTable.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        // Open this row
        row.child( tasksSub(row.data(), row.id_action_plan, row.id_obtaining_period) ).show();
        tr.addClass('shown');
    }
});
/* Formatting function for row details - modify as you need */
function tasksSub(subrequirement, idActionPlan, idPeriod) {
    // `subrequirement` is the original data object for the row
    let t = `
        <button type="button" id="buttonAddTask" class="btn btn-success float-right" data-toggle="tooltip" 
            title="Agregar tarea" onclick="addTask(${idActionPlan}, ${idPeriod});">
            Agregar tarea
        </button>`;
    if (subrequirement.tasks.length != 0) {
        t += `
        <table class="table table-bordered" cellspacing="0" width="100%">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center">Identificador</th>
                    <th class="text-center">Tarea</th>
                    <th class="text-center">Responsable</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Periodo</th>
                </tr>
            </thead>
            <tbody>`;
        subrequirement.tasks.forEach(element => {
            t += `
            <tr>
                <td class="text-center">${ (element.title === null) ? '' : element.title }</td>
                <td class="text-center">${ (element.task === null) ? '' : element.task }</td>
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
                <td class="text-center">${ (element.status === null) ? '' : element.status }</td>
                <td class="text-center">${ (element.period === null) ? '' : element.period }</td>
                <td class="text-center">
                    ${actionsTasks(task, 'sub_action_plan')}
                </td>
            </tr>`;
        });
        t += `</tbody>
        </table>`;
    }else{
        t += `<table class="table table-bordered" cellspacing="0" width="100%">
            <td class="text-center">Subrequerimiento Sin Tareas</td>
        </table>`;
    }
    return t;
}
/**
 * Reload sub action table
 */
const reloadSubAction = () => { subActionTable.ajax.reload() }
const reloadSubActionKeepPage = () => { subActionTable.ajax.reload(null, false) }

</script>