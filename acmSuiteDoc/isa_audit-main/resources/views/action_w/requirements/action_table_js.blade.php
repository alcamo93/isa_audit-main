<script>
/**
 * Close action 
 */
function closeAction() {
    currentAR.idActionRegister = null;
    currentAR.idAuditProcess = null;
    currentAR.customerTrademark = null;
    currentAR.corporateTradename = null;
    currentAR.auditProcess = null;
    currentAR.scopeAudit = null;
    currentAR.evaluateRisk = null;
    filtersDates.dateMin = null;
    filtersDates.dateMax = null;
    flatpickr('#filterDates').redraw()
    cleanForm('#actionFilterForm');
    $('.loading').removeClass('d-none');
    $('.action').addClass('d-none');
    setTimeout(() => {
        $('.loading').addClass('d-none');
        $('.contracts').removeClass('d-none');
    }, 1000)
}
/*
 * show all requirements
 */ 
function showActionPlan(idActionRegister, idAuditProcess, customer, 
        corporate, auditProcess, idScope, scopeAudit, scopeSpecification, 
        evaluateRisk, idCustomer, idCorporate) {
    // Set filter Matters
    $('.loading').removeClass('d-none');
    $('.contracts').addClass('d-none');
    currentAR.idActionRegister = idActionRegister;
    setMatters()
    .then(res => {
        currentAR.idAuditProcess = idAuditProcess;
        currentAR.customerTrademark = customer;
        currentAR.corporateTradename = corporate;
        currentAR.idCustomer = idCustomer;
        currentAR.idCorporate = idCorporate;
        currentAR.auditProcess = auditProcess;
        currentAR.scopeAudit = (idScope == 2) ? `${scopeSpecification}: ${scopeAudit}` : scopeAudit;
        currentAR.evaluateRisk = (evaluateRisk == 1) ? true : false;
        // Set Headers
        $('.customerTitle').html(currentAR.customerTrademark);
        $('.corporateTitle').html(currentAR.corporateTradename);
        $('.auditTitle').html(currentAR.auditProcess);
        $('.scopeTitle').html(currentAR.scopeAudit);
        if (tables.actions == null) tables.actions = actionTableInstance();
        else tables.actions.ajax.reload();
        if (tables.expired == null) tables.expired = expiredTableInstance();
        else tables.expired.ajax.reload();
        return getCounterService()
    })
    .then(res => {
        $('.action').removeClass('d-none');
        $('.loading').addClass('d-none');
    })
    .catch(e => {
        console.error(e);
        toastAlert('No se puede abrir el Plan de Acción', 'error');        
        $('.contracts').removeClass('d-none');
        $('.loading').addClass('d-none');
    })
}
function reloadTables(){
    tables.actions.ajax.reload();
    tables.expired.ajax.reload();
}
/*
 * Action table
 */
function actionTableInstance() {
    return $('#actionTable').DataTable({
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
                    const risks = data.risks;
                    let list = '';
                    if (risks.length > 0) {
                        risks.forEach(e => {
                            const c = e.risk_category || '';
                            const i = e.interpretation || '';
                            list += `<span class="badge badge-info text-white">${c}: ${i}</span><br>`
                        });   
                    }
                    else list = `<span class="badge badge-secondary text-white">N/A</span><br>`;
                    return list;
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 3
            },
            {
                data: data => {
                    const title = (data.subrequirement != null) ? data.subrequirement.no_subrequirement : data.requirement.no_requirement;
                    const actionEnabled = (CREATE || MODIFY) ? `
                            title="Abrir para cambiar prioridad"
                            onclick="changePriority('No: ${title}', ${data.id_action_plan}, ${data.id_priority})"` : '';
                    const linkPriority = `
                        <a class="btn btn-link go-to-process"
                            data-toggle="tooltip" 
                            ${actionEnabled}>
                            ${data.priority.priority}
                        </a>`;
                    return linkPriority
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 4
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
                            onclick="showTextService('No: ${title}', '${data.id_action_plan}')">
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
                    if (data.init_date != null) {
                        date = formatDate(data.init_date, 'date');
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
                    let date = null
                    if (data.close_date != null) {
                        date = formatDate(data.close_date, 'date');
                    }
                    const linkDate = `<a class="btn btn-link">
                        ${date || 'Sin fecha definida'}
                        </a>`;
                    return linkDate
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 7
            },
            {
                data: data => {
                    const index = data.id_status || 0; 
                    const {color, status} = statusAP[index];
                    return `<span class="badge badge-${color} text-white">${status}</span>`; 
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 8
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
                                data-toggle="tooltip">
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
                                ${enabledSet}>
                                <i class="fa fa-user-plus la-lg"></i>
                            </a>`;
                    };
                    return btn;
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 9
            },
            {
                data: data => {
                    const btnPanel = (data.users.length > 0) ? `
                        <a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Panel" 
                            onclick="showMainPanel(${data.id_action_plan}, 'action')">
                            <i class="fa fa-folder-open-o la-lg"></i>
                        </a>` : '';   
                    const name = (data.subrequirement != null) ? data.subrequirement.subrequirement : data.requirement.requirement;
                    const btnExpired = (data.real_close_date == null && data.id_status == 25) ? `
                        <a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Establecer detalles de vencimiento" 
                            onclick="setExpired('${name}', ${data.id_action_plan}, '${data.close_date}')">
                            <i class="fa fa-calendar-times-o la-lg"></i>
                        </a>` : '';
                    return btnPanel+btnExpired;
                },
                className: 'td-actions text-center',
                orderable: false,
                targets: 10
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
const reloadAction = () => { 
    getCounterService()
    .then(data => {
        tables.actions.ajax.reload() 
    })
    .catch(e => {
        toastAlert(e, 'error');
    })
}
const reloadActionKeepPage = () => { 
    getCounterService()
    .then(data => {
        tables.actions.ajax.reload(null, false)
    })
    .catch(e => {
        toastAlert(e, 'error');
    })
}

function changePriority(title, idActionPlan, idPriority) {
    const inputOptions =  {
        '1': 'Baja',
        '2': 'Media',
        '3': 'Alta'
    }
    const { value } = Swal.fire({
        title: `Selecciona la Prioridad para el requerimiento ${title}`,
        input: 'radio',
        inputOptions: inputOptions,
        showCancelButton: true,
        inputValidator: (value) => {
            if (value) {
                setPriority(idActionPlan, value)
                .then(data => {
                    toastAlert(data.msg, data.status);
                    if (data.status == 'success') {
                        reloadActionKeepPage();
                    }
                })
                .catch(e => {
                    toastAlert(e, 'error');
                })
            }
        }
    })
    $('input[name=swal2-radio][value='+idPriority+']').prop('checked', true); 
}
</script>