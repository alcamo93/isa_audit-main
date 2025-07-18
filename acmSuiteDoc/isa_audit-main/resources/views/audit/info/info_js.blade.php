<script>
/*************************** Aspects to Audit ***************************/
/**
 * Get Aspects by matter
 */
function setAspectsToAudit() {
    document.querySelector('#customerTitle').innerHTML = currentAR.customer;
    document.querySelector('#corporateTitle').innerHTML = currentAR.corporate;
    $('.main-register').addClass('d-none');
    $('.loading').removeClass('d-none');
    // set matter in contract
    getAuditMatters()
    .then(() => {
        $('.loading').addClass('d-none');
        $('.matters-list-card').removeClass('d-none');
        reloadaspectsRegister();
    })
    .catch(() => {
        $('.main-register').removeClass('d-none');
        $('.loading').addClass('d-none');
        okAlert('Sin Materias para evaluar', 'Todas las Materias de la Auditoría fueron eliminadas', 'error');
    });
}
/**
 * Show buttons
 */
function optionButtons(){
    switch (currentStatus.audit.currentStatus) {
        case AUDITED:
            $('#finishAudit').removeClass('d-none');
            $('#reportAudit').addClass('d-none');
            break;
        case FINISHED:
            $('#finishAudit').addClass('d-none');
            $('#reportAudit').removeClass('d-none');
            break;
        default:
            $('#finishAudit').addClass('d-none');
            $('#reportAudit').addClass('d-none');
            break;
    }
}
/**
 * set status in progress bar
 */
function setProgressBarByMatter(){
    return new Promise((resolve, reject) => {
        const idAuditMatter = parseInt(document.querySelector('#idAuditMatter').value);
        if (idAuditMatter != 0) {
            $('.progressGlobal').addClass('d-none');
            const matterIndex = currentStatus.matters.findIndex( e => e.id_audit_matter === idAuditMatter );
            const { audited, total: total_aspects } = currentStatus.audit;
            if (matterIndex == -1) reject(false);
            const { total, matter, status } = currentStatus.matters[matterIndex];
            $('#progressBarMatter').css({width: `${total}%`});
            document.querySelector('#barPercentMatter').innerHTML = `${total}%`;
            document.querySelector('#matterTitle').innerHTML = matter.matter;
            document.querySelector('#advanceTitle').innerHTML = `${audited} / ${total_aspects}`;
            document.querySelector('#statusTitle').innerHTML = `${status.status}`;
            document.querySelector('#complianceTitle').innerHTML = `${total}%`;
            $('.progressMatterArea').removeClass('d-none');
            resolve(true);
        }
        else {
            $('.progressMatterArea').addClass('d-none');
            $('#progressBarMatter').css({width: `${currentStatus.global}%`});
            document.querySelector('#barPercentMatter').innerHTML = `${currentStatus.global}%`;
            document.querySelector('#globalTitle').innerHTML = `${currentStatus.global}%`;
            $('.progressGlobal').removeClass('d-none');
            resolve(true);
        }
    });
}

/**
 * Get audit aspects by matter
 */
const aspectsRegisterTable = $('#aspectsRegisterTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: "/assets/lenguage.json"
    },
    ajax: { 
        url: '/audit/resgisters/aspects', 
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.idAuditRegister = currentAR.idAuditRegister,
            data.idAuditMatter = document.querySelector('#idAuditMatter').value,
            data.filterIdStatus = document.querySelector('#filterIdStatusAspect').value
        }
    },
    columns: [
        { data: 'matter', className: 'text-center',orderable : true },
        { data: 'aspect', className: 'text-center',orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'application_type', className: 'text-center', orderable : true },
        { data: 'total', className: 'text-center', orderable : true },
        { data: 'id_aspect', className: 'text-center td-actions', orderable : false, visible: true }
    ],
    columnDefs: [
        {
            render: ( data, type, row ) => {
                let color = '';
                switch (data) {
                    case 'Sin auditar':
                        color = 'warning';
                        break;
                    case 'Auditado':
                        color = 'success';
                        break;
                    case 'Auditando':
                    case 'Evaluando':
                        color = 'info';
                        break;
                    case 'Finalizado':
                        color = 'success';
                        break;
                    default:
                        break;
                }
                return `<span class="badge badge-${color} text-white">${data}</span>`; 
            },
            targets: 2
        },
        {
            render: ( data, type, row ) => {
                return (data === null) ? '' :`<span class="badge badge-primary text-white">${data}</span>`;
            },
            targets: 3
        },
        {
            render: ( data, type, row ) => {
                return `${data}%`;
            },
            targets: 4
        },
        {
            render: (data, type, row) => {
                // let action = '';
                // if(row.id_status != FINISHED){
                let action = `"validateSpecificRequirements(${row.id_aspect}, '${row.matter}', '${row.aspect}', 
                         ${row.id_contract}, ${row.id_audit_aspect}, ${row.id_status}, ${row.id_application_type}, ${row.id_audit_processes})"`
                // let action = `"getRequirementsAspect(${row.id_aspect}, '${row.matter}', '${row.aspect}', 
                //             ${row.id_contract}, ${row.id_audit_aspect}, ${row.id_status}, ${row.id_application_type})"` 
                // }else{
                //     action += `"okAlert('Aspecto Finalizado', 'Una vez finalizado la auditoria no es posible editar', 'info')"`;
                // }
                let btnAction = (MODIFY) ? `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Auditar aspecto" 
                                    onclick=${action}>
                                    <i class="fa fa-check-square-o fa-lg"></i>
                                </button>`:'';
                let btnDelete = (DELETE) ? `<button class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar aspecto" 
                                    onclick="deleteAspect('${row.aspect}', ${row.id_audit_matter}, ${row.id_audit_aspect}, ${row.id_audit_register})">
                                    <i class="fa fa-times fa-lg"></i>
                                </button>`:'';
                return btnAction+btnDelete;
            },
            targets: 5
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
/**
 * Reload aspects and execute progressMatter
 */
const reloadaspectsRegister = () => { 
    progressMatter()
    .then(res => setProgressBarByMatter() )
    .then(res => {
        optionButtons();
        aspectsRegisterTable.ajax.reload(null, true); 
    })
    .catch(e => {
        console.log(e)
        toastAlert('No se pudo obtener estatus', 'error');
        aspectsRegisterTable.ajax.reload(null, true); 
    })
}
/**
 * close aspects lists
 */
function closeAuditMatters(){
    window.location.href = `/v2/process/view`;
}
/**
 * Delete Aspect
 */
function deleteAspect(aspectName, idAuditMatter, idAuditAspect, idAuditRegister){
    Swal.fire({
        title: `¿Estás seguro de eliminar "${aspectName}"?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $('.loading').removeClass('d-none');
            deleteAspectService(idAuditMatter, idAuditAspect)
            .then(data => {
                toastAlert(data.msg, data.status);
                return getAuditMatters(idAuditRegister)
            })
            .then(res => {
                reloadaspectsRegister();
                $('.loading').addClass('d-none');
            })
            .catch(() => {
                $('.loading').addClass('d-none');
                toastAlert('No se pudo obtener las materias', 'error');
            });
        }
    });
}

$(document).on('click', '#reportAuditProgress', function () {
    window.open(`/audit/progress/report/${currentAR.idAuditRegister}`);
});
/**
 * report documents in requirements
 */
function reportAuditDocument(){
    window.open(`/audit/document/report/${currentAR.idAuditRegister}`);
}
/**
 * Report audit
 */
function reportAudit(){
    window.open(`/audit/report/${currentAR.idAuditRegister}`);
}
/**
 * Set data in action plan
 */
function setInActionPlan() {
    Swal.fire({
        title: '¿Estás seguro de marcar como finalizado?',
        text: "Una vez finalizado no se podra editar nada de auditoria",
        icon: 'warning',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, finalizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $('.loading').removeClass('d-none');
            setInActionPlanService()
            .then(data => {
                $('.loading').addClass('d-none');
                if (data.status == 'success' || data.status == 'warning') {
                    okAlert(data.title, data.msg, data.status);
                    reloadaspectsRegister();
                }else{
                    toastAlert(data.msg, data.status);
                }
            })
            .catch(e => {
                $('.loading').addClass('d-none');
                toastAlert(e, 'error');
            })
        }
    });
}

function validateSpecificRequirements(idAspect, matter, aspect, idContract, idAuditAspect, idStatus, idApplicationType, idAuditProcess) {
    $('.loading').removeClass('d-none');
    validateSpecificRequirementsService(idAuditProcess, idAspect)
    .then(({data}) => {
        $('.loading').addClass('d-none');
        const { has_evaluate, has_requirements } = data;
        if (has_evaluate && has_requirements == false && idStatus == NOT_AUDITED) {
            $('.loading').addClass('d-none');
            return showQuestionEmpty(idAspect, matter, aspect, idContract, idAuditAspect, idStatus, idApplicationType);
        }
        getRequirementsAspect(idAspect, matter, aspect, idContract, idAuditAspect, idStatus, idApplicationType)
    })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert(e, 'error');
    })
}

function showQuestionEmpty(idAspect, matter, aspect, idContract, idAuditAspect, idStatus, idApplicationType) {
    Swal.fire({
        title: 'No se tienen requerimientos específicos de Condicionantes, Actas u otros para este aspecto',
        text: "Elegiste evaluar estos requerimientos, por favor antes de iniciar dirígete al módulo Condicionantes, Actas u otros para agregarlos o ¿deseas continuar sin estos registros?",
        icon: 'warning',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, continuar',
        cancelButtonText: 'No, cancelar'
    }).then((result) => {
        if (result.value) {
            getRequirementsAspect(idAspect, matter, aspect, idContract, idAuditAspect, idStatus, idApplicationType)
        }
    });
}
</script>
