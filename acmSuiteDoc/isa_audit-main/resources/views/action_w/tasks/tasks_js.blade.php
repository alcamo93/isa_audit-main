<script>
/**
 * Close action 
 */
function closeTasks() {
    $('.loading').removeClass('d-none');
    $('.tasks').addClass('d-none');
    reloadTables();
    setTimeout(() => {
        $('.loading').addClass('d-none');
        $('.action').removeClass('d-none');
    }, 1000)
}
/*
 * Action table
 */
function tasksTableInstance(){
    return $('#tasksTable').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        dom: `<'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
        language: {
            url: '/assets/lenguage.json'
        },
        ajax: { 
            url: '/action/tasks',
            type: 'POST',
            data:  (data) => {
                data._token = document.querySelector('meta[name="csrf-token"]').content,
                data.idActionPlan = currentTask.idActionPlan,
                data.section = currentAP.section
            }
        },
        columnDefs: [
            {
                data: data => {
                    return data.title
                },
                className: 'text-center',
                orderable: true,
                targets: 0
            },
            {
                data: data => {
                    const limit = 17;
                    const textTruncate = (data.task.length > limit) ? `${data.task.substr(0,limit)}…` : data.task;
                    const linkFinding = `
                        <a class="btn btn-link go-to-process"
                            data-toggle="tooltip" 
                            title="Ver Tarea completa"
                            onclick="showText('Tarea: ${data.title}', '${data.task}', false)">
                            ${textTruncate}
                        </a>`;
                    return linkFinding;
                },
                className: 'text-center truncate',
                orderable: true,
                targets: 1
            },
            {
                data: data => {
                    let btn = '';
                    if (data.users.length > 0) {
                        const res = data.users.find(e => e.level == 1);
                        const {first_name, second_name, last_name} = res.user.person;
                        name = `${first_name} ${second_name} ${last_name}`;
                        img = `${res.user.picture}`;
                        btn = `
                            <a class="btn btn-link go-to-process" 
                                data-toggle="tooltip" 
                                title="Resposable"
                                <div>
                                    <img width="50px" class="avatar" src="/assets/img/faces/${img}">
                                </div>
                                <div>${name}</div>
                            </a>`;
                    }
                    else {
                        btn = `<span class="badge badge-secondary text-white">N/A</span>`;
                    };
                    return btn;
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 2
            },
            {
                data: data => {
                    const currentStatus = (data.task_expired && currentAP.section != 'action') ? data.task_expired.id_status : data.id_status;
                    const index = currentStatus || 0; 
                    const {color, status} = statusTask[index];
                    return `<span class="badge badge-${color} text-white">${status}</span>`; 
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 3
            },
            {
                data: data => {
                    let date = null
                    if (data.init_date != null) {
                        date = formatDate(data.init_date, 'date');
                    }
                    const linkDate = `<a class="btn btn-link">
                        ${date || 'Sin fecha definida'}
                        </a>`;
                    return linkDate
                },
                className: 'td-actions text-center',
                orderable: true,
                targets: 4
            },
            {
                data: data => {
                    const currentDate = (data.task_expired && currentAP.section != 'action') ? data.task_expired.close_date : data.close_date;
                    let date = null
                    if (currentDate != null) {
                        date = formatDate(currentDate, 'date');
                    }
                    const linkDate = `<a class="btn btn-link">
                        ${date || 'Sin fecha definida'}
                        </a>`;
                    return linkDate
                },
                className: 'td-actions text-center',
                orderable: true,
                targets: 5
            },
            {
                render: (data, type, row) => {
                    const permissionProfile = permission[ID_PROFILE_TYPE]['modifyTask'];
                    const sectionAction = (currentAP.section == 'action') ? true : false;
                    const block = (row.block == 1) ? true : false;
                    const stayStageExpired = (row.stage == 2 && currentAP.section == 'expired') ? true : false;
                    const stayStageAction = (row.stage == 1 && currentAP.section == 'action') ? true : false;
                    const actionInSection = (stayStageExpired || stayStageAction);
                    // btn action
                    if (currentAP.blockTask || !row.permission) {
                        const btnView =
                            `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar tarea" 
                                onclick="showTask(${row.id_task})">
                                <i class="fa fa-eye fa-lg"></i>
                            </a>`;
                        return btnView
                    }
                    else {
                        const btnEdit = (MODIFY) ? 
                        `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar tarea" 
                            onclick="editTask('${row.title}', ${row.id_task}, ${row.block})">
                            <i class="fa fa-edit fa-lg"></i>
                        </a>` : '';
                        const btnDelete = (DELETE && actionInSection ) ? 
                            `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar tarea" 
                                onclick="deleteTask(${row.id_task}, '${row.title}')">
                                <i class="fa fa-times fa-lg"></i>
                            </a>`:'';
                        const idFile = (row.file) ? row.file.id_file : null;
                        const btnFile = `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Documentos" 
                                onclick="setFile('${row.title}', ${row.id_task}, ${row.id_status}, ${idFile})">
                                <i class="fa fa-file-pdf-o fa-lg"></i> 
                            </a>`;
                        const btnComment = `<a class="btn btn-primary btn-link btn-xs" data-toggle="tooltip" title="Agregar comentario" 
                                onclick="addCommnet('${row.title}', ${row.id_task}, ${actionInSection})">
                                <i class="fa fa-comments fa-lg"></i>
                            </a>`;
                            return btnFile+btnEdit+btnComment+btnDelete;
                    }
                },
                className: 'td-actions text-center',
                targets: 6
            }
        ],
        createdRow: (row, data, dataIndex) => {
            $(row).find(".truncate").each(function(){
                $(this).attr("title", this.innerText);
            });
        },
        drawCallback: (settings) => {
            // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
            $('[data-toggle="tooltip"]').on('click', function () {
                $(this).tooltip('hide')
            })
        }
    });
}
/**
 * Reload sub action table
 */
const reloadTasks = () => { tables.tasks.ajax.reload(null, false) }
const reloadTasksKeepPage = () => { tables.tasks.ajax.reload() }
</script>