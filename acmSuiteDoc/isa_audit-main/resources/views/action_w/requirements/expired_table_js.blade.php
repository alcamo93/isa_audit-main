<script>
/*
 * Action table
 */
function expiredTableInstance() {
    return $('#expiredTable').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
        language: {
            url: '/assets/lenguage.json'
        },
        ajax: { 
            url: '/action/action-expired',
            type: 'POST',
            data: (data) => {
                data._token = document.querySelector('meta[name="csrf-token"]').content,
                data.idActionRegister = currentAR.idActionRegister,
                data.idAuditProcess = currentAR.idAuditProcess,
                data.idMatter = document.querySelector('#filterIdMatter').value,
                data.idAspect = document.querySelector('#filterIdAspect').value,
                data.requirementName = document.querySelector('#filterRequirement').value,
                data.idStatus = document.querySelector('#filterIdStatus').value,
                data.idPriority = document.querySelector('#filterIdPriority').value,
                data.initDate = filtersDates.dateMin,
                data.endDate = filtersDates.dateMax,
                data.userName = document.querySelector('#filterUserName').value
            }
        },
        columnDefs: [
            {
                data: data => {
                    return `Aspecto: ${data.aspect.aspect}`
                },
                className: 'text-center',
                visible: false,
                orderable: false,
                targets: 0
            },
            {
                data: data => {
                    return data.requirement.no_requirement
                },
                className: 'text-center',
                visible: false,
                orderable: false,
                targets: 1
            },
            {
                data: data => {
                    const cell = (data.subrequirement != null) ? data.subrequirement.subrequirement : data.requirement.requirement;
                    return cell
                },
                className: 'td-actions text-justify',
                orderable: false,
                targets: 2
            },
            {
                data: data => {
                    const title = (data.subrequirement != null) ? data.subrequirement.no_subrequirement : data.requirement.no_requirement;
                    const linkPriority = `
                        <a class="btn btn-link go-to-process"
                            data-toggle="tooltip" 
                            title="Abrir para cambiar prioridad"
                            onclick="changePriority('No: ${title}', ${data.id_action_plan}, ${data.id_priority})">
                            ${data.priority.priority}
                        </a>`;
                    return linkPriority
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 3
            },
            {
                data: data => {
                    const limit = 17;
                    const text = data.finding || 'Sin Hallazgo';
                    const textTruncate = (text.length > limit) ? `${text.substr(0,limit)}…` : text;
                    const title = (data.subrequirement != null) ? data.subrequirement.no_subrequirement : data.requirement.no_requirement;
                    const linkFinding = `
                        <a class="btn btn-link go-to-process"
                            data-toggle="tooltip" 
                            title="Ver hallazgo completo"
                            onclick="showText('No: ${title}', '${data.finding}')">
                            ${textTruncate}
                        </a>`;
                    return linkFinding
                },
                className: 'td-actions text-justify truncate',
                orderable: false,
                targets: 4
            },
            {
                data: data => {
                    const limit = 17;
                    const text = data.expired.cause || 'Sin Causa';
                    const textTruncate = (text.length > limit) ? `${text.substr(0,limit)}…` : text;
                    const title = (data.subrequirement != null) ? data.subrequirement.no_subrequirement : data.requirement.no_requirement;
                    const linkFinding = `
                        <a class="btn btn-link go-to-process"
                            data-toggle="tooltip" 
                            title="Ver causa de desviación"
                            onclick="showText('No: ${title}', '${data.expired.cause}', false)">
                            ${textTruncate}
                        </a>`;
                    return linkFinding
                },
                className: 'td-actions text-justify truncate',
                orderable: false,
                targets: 5
            },

            {
                data: data => {
                    let date = null
                    if (data.expired.real_close_date != null) {
                        date = formatDate(data.expired.real_close_date, 'date');
                    }
                    const linkDate = `<a class="btn btn-link">
                        ${date || 'Sin fecha definida'}
                        </a>`;
                    return linkDate
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 6
            },
            {
                data: data => {
                    const index = data.expired.id_status || 0; 
                    const {color, status} = statusAP[index];
                    return `<span class="badge badge-${color} text-white">${status}</span>`; 
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 7
            },
            {
                data: data => {
                    let btn = '';
                    if (data.users.length > 0) {
                        const res = data.users.find(e => e.level == 1);
                        const {first_name, second_name, last_name} = res.user.person;
                        name = `${first_name} ${second_name} ${last_name}`;
                        img = `${res.user.picture}`;
                        const enabledSet = (CREATE || MODIFY) ? `
                                title="Abrir sección Resposables"
                                onclick="asignedUser(${data.id_action_plan})"` : '';
                        btn = `
                            <a class="btn btn-link go-to-process" 
                                ${enabledSet}
                                data-toggle="tooltip" 
                                <div>
                                    <img width="50px" class="avatar" src="/assets/img/faces/${img}">
                                </div>
                                <div>${name}</div>
                            </a>`;
                    }
                    else {
                        const enabledSet = (CREATE || MODIFY) ? `
                                title="Asignar Responsables" 
                                onclick="asignedUser(${data.id_action_plan})"` : '';
                        btn = `
                            <a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" 
                                ${enabledSet}
                                <i class="fa fa-user-plus la-lg"></i>
                            </a>`;
                    };
                    return btn;
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 8
            },
            {
                data: data => {
                    let btnPanel = (data.users.length > 0) ? `
                            <a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Panel" 
                                onclick="showMainPanel(${data.id_action_plan}, 'expired')">
                                <i class="fa fa-folder-open-o la-lg"></i>
                            </a>` : '';
                    const name = (data.subrequirement != null) ? data.subrequirement.subrequirement : data.requirement.requirement;
                    const btnExpired = (data.expired.id_status == 25) ? `
                        <a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Establecer detalles de nuevo vencimiento" 
                            onclick="setAgainExpired('${name}', ${data.expired.id_action_expired}, '${data.expired.real_close_date}')">
                            <i class="fa fa-calendar-times-o la-lg"></i>
                        </a>` : '';
                    return btnPanel+btnExpired;
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 9
            }
        ],
        rowGroup: {
            dataSrc: ['aspect.aspect', 'requirement.no_requirement'],
            startRender: (rows, group, level) => {
                return (level === 0) ? `Aspecto: ${group}` : group
            },
        },
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
 * Reload action table
 */
const reloadExpired = () => { tables.expired.ajax.reload() }
const reloadExpiredKeepPage = () => { tables.expired.ajax.reload(null, false) }
/**
 * Set again expired
 */
</script>