<script>
/*************************** Corporates to Audit ***************************/
let users = [];
let idProcessCurrent = null;
/**
 * Audit register tables
 */
const processesTable = $('#processesTable').DataTable({processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: "/assets/lenguage2.json"
    },
    ajax: { 
        url: '/processes',
        type: 'POST',
        data: (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content
            data.filterIdCustomer = document.querySelector('#filterIdCustomer').value,
            data.filterIdCorporate = document.querySelector('#filterIdCorporate').value,
            data.fProcess = document.querySelector('#filterProcess').value,
            data.filterIdUser = ID_USER;
        }
    },
    columns: [
        { data: 'audit_processes', className: 'text-center', orderable : true },
        { data: 'corp_tradename', className: 'text-center', orderable : true },
        { data: 'id_scope', className: 'text-center', orderable : true },
        { data: 'id_status', className: 'text-center', orderable : true },
        { data: 'id_audit_processes', className: 'text-center', orderable : false, visible: true },
        { data: 'id_audit_processes', className: 'text-center', orderable : false, visible: true }
    ],
    columnDefs: [
        {
            render: ( data, type, row ) => {
                const departament = 2;
                let scope = '';
                if (row.id_scope == departament) {
                    scope = `${row.scope}: ${row.specification_scope}`;
                } else scope = row.scope;
                return scope;
            },
            targets: 2
        },
        {
            render: ( data, type, row ) => {
                let colorAplicability = '';
                let aplicability = '';
                if (row.status_aplicability) {
                    switch (row.status_aplicability) {
                        case 'Sin clasificar':
                            colorAplicability = 'warning';
                            break;
                        case 'Clasificado':
                            colorAplicability = 'success';
                            break;
                        case 'Evaluando':
                            colorAplicability = 'info';
                            break;
                        case 'Finalizado':
                            colorAplicability = 'success';
                            break;
                        default:
                            break;
                    }
                    aplicability = `<span class="badge badge-${colorAplicability} text-white">Aplicabilidad: ${row.status_aplicability}</span></br>`
                }
                let colorAudit = '';
                let audit = '';
                if (row.status_audit) {
                    switch (row.status_audit) {
                            case 'Sin auditar':
                            colorAudit = 'warning';
                            break;
                        case 'Auditado':
                            colorAudit = 'success';
                            break;
                        case 'Auditando':
                        case 'Evaluando':
                            colorAudit = 'info';
                            break;
                        case 'Finalizado':
                            colorAudit = 'success';
                            break;
                        default:
                            break;
                    }
                    audit = `<span class="badge badge-${colorAudit} text-white">Auditoría: ${row.status_audit}</span></br>`
                }
                
                return aplicability+audit;
            },
            targets: 3
        },
        {
            render: ( data, type, row ) => {
                let btnAplicability = `<button class="btn btn-link go-to-process" data-toggle="tooltip" title="Ir a Aplicabilidad" 
                                    onclick="openAplicability(${row.id_aplicability_register})">
                                    <i class="fa fa-external-link fa-lg"></i>
                                    Aplicabilidad
                                </button>`;
                let btnAudit = '';
                if (row.status_audit) {
                    btnAudit += `<button class="btn btn-link go-to-process" data-toggle="tooltip" title="Ir a Auditoría" 
                                    onclick="openAudit(${row.id_audit_register})">
                                    <i class="fa fa-external-link fa-lg"></i>
                                    Auditoría
                                </button>`;
                }
                return btnAplicability+'<br>'+btnAudit;
            },
            targets: 4
        },
        {
            render: (data, type, row) => {  
                let btnEditProccess = '';
                let btnDeleteProcess = '';
                if (row.status_aplicability == 'Sin clasificar') {   ;
                    btnEditProccess += `<button class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar Auditoría" 
                                    onclick="openEditProcesses('${row.audit_processes}', '${row.id_audit_processes}')">
                                    <i class="fa fa-edit fa-lg"></i>
                                </button>`;
                }
                if (row.status_audit != 'Finalizado') {
                    btnDeleteProcess += `<button class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar Auditoría" 
                                    onclick="deleteProcesses('${row.audit_processes}', '${row.id_audit_processes}')">
                                    <i class="fa fa-times fa-lg"></i>
                                </button>`;
                }
                let btnShow = `<button class="btn btn-info  btn-link btn-xs" data-toggle="tooltip" title="Mostrar registro de Auditoría" 
                                    onclick="showProcess('${row.audit_processes}', '${row.id_audit_processes}')">
                                    <i class="fa fa-eye fa-lg"></i>
                                </button>`;
                return btnShow+btnEditProccess+btnDeleteProcess;
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
const reloadProcesses = () => { processesTable.ajax.reload() } //filtros
const reloadProcessesKeepPage = () => { processesTable.ajax.reload(null, false) } //actualizar
/**
 * service users
 */
function getUsersService(idCorporate) {
    return new Promise((resolve, reject) => {
        $.get(`/processes/users/${idCorporate}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idCorporate: idCorporate
        },
        (data) => {
            resolve(data)
        })
        .fail(e => {
            reject(e.statusText)
        })   
    });
}
/**
 * Get leader
 */
function getLeaders(idCorporate, idSelectorLeader, idSelectorAuditors) {
    return new Promise((resolve, reject) => {
        if (idCorporate != '') {
            getUsersService(idCorporate)
            .then(data => {
                users = data;
                let html = `<option value=""></option>`;
                if (data.length > 0) {
                    data.forEach(e => {
                        html = html+`<option value="${e.id_user}">${e.complete_name}</option>`;
                    });
                }
                else {
                    html = `<option disabled value="">No cuentas con usuarios</option>`;
                }
                document.querySelector(idSelectorLeader).innerHTML = html;
                resolve(true);
            })
            .catch(e => {
                toastAlert(e.statusText, 'error');
                reject('No se pudo obtener los Auditores Lideres');
            })   
        }
        else {
            document.querySelector(idSelectorLeader).innerHTML = `<option value="" disabled>Seleccione una Planta</option>`;
            getAuditors(0, idSelectorAuditors).then(()=>{
                resolve(true);
            });
        }
    });
}
/**
 * Get auditor users
 */
function getAuditors(idUser, idSelectorAuditors) {
    return forceGetAuditors(idUser, idSelectorAuditors)
    .then(() => {
        return new Promise((resolve, reject) => {
            if (idUser != 0) {
                forceGetAuditors(idUser, idSelectorAuditors)
                let auditors = users.filter( e => e.id_user != idUser);
                let html = ``;
                if (auditors.length > 0) {
                    auditors.forEach(e => {
                        html = html+`<option value="${e.id_user}">${e.complete_name}</option>`;
                    });
                }
                else {
                    html = `<option disabled value="">No cuentas con más usuarios</option>`;
                }
                document.querySelector(idSelectorAuditors).innerHTML = html;
                $(idSelectorAuditors).selectpicker('refresh');
                resolve(true);
            }
            else {
                document.querySelector(idSelectorAuditors).innerHTML = '';
                $(idSelectorAuditors).selectpicker('refresh');
                resolve(true);
            }    
        });
    })
    .catch(e => {
        reject(false);
        toastAlert(e);
    })
}
/**
 * type profile
 */
function forceGetAuditors(idUser, idSelectorAuditors) {
    return new Promise((resolve, reject) => {
        if (ID_PROFILE_TYPE == 4 || ID_PROFILE_TYPE == 5) {
            getUsersService(ID_CORPORATE)
            .then(data => {
                users = data;
                resolve(true)
            })
            .catch(e => {
                reject(false);
                toastAlert(e);
            });
        }
        else {
            resolve(true);
        }
    });
}
/**
 * open option specification
 */
function showField(idScope, idSelectorSpecification) {
    let departament = 2;
    if (idScope == departament) {
        $('.specificationDepartament').removeClass('d-none');
        $(idSelectorSpecification).attr('data-rule-required', true);
    }
    else {
        document.querySelector(idSelectorSpecification).value = '';
        $('.specificationDepartament').addClass('d-none');
        $(idSelectorSpecification).attr('data-rule-required', false);
    }
}
/**
 * Set option multiple in auditors
 */
function setAuditors(auditors, idSelectorAuditors){
    return new Promise((resolve, reject) => {
        let options = Array.from(document.querySelectorAll(`${idSelectorAuditors} option`));
        auditors.forEach(aud => {
            options.find(opt => opt.value == aud.id_user).selected = true;
        });
        $(idSelectorAuditors).selectpicker('refresh');
        resolve(true);
    });
}
/**
 * Set option multiple in auditors
 */
function setAspects(aspects, idSelectorAspects){
    return new Promise((resolve, reject) => {
        let options = Array.from(document.querySelectorAll(`${idSelectorAspects} option`));
        aspects.forEach(function(asp) {
            options.find(opt => opt.value == asp.id_aspect).selected = true;
        });
        $(idSelectorAspects).selectpicker('refresh');
        resolve(true);
    });
}
/**
 * Open modal add processes
 */
function openAddProcesses() {
    document.querySelector('#setProcessesForm').reset();
    $('#setProcessesForm').validate().resetForm();
    $('#setProcessesForm').find(".error").removeClass("error");
    $('.selectpicker').selectpicker('deselectAll');
    showField(1, '#s-specification');
    $('#addModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set processes form 
 */
$('#setProcessesForm').submit( (event) => {
    event.preventDefault();
    if($('#setProcessesForm').valid()) {
        showLoading('#addModal');
        //handler notificaction
        $.post('/processes/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idCustomer: document.querySelector('#s-idCustomer').value,
            idCorporate: document.querySelector('#s-idCorporate').value,
            processes: document.querySelector('#s-processes').value,
            idScope: document.querySelector('#s-idScope').value,
            specification: document.querySelector('#s-specification').value,
            idLeader: document.querySelector('#s-idLeader').value,
            setRisk: document.querySelector('#s-risk').checked,
            auditors: $('#s-auditors').val(),
            aspects: $('#s-aspects').val()
        },
        (data) => {
            showLoading('#addModal');
            switch (data.status) {
                case 'success':
                    toastAlert(data.msg, data.status);
                    reloadProcessesKeepPage();
                    $('#addModal').modal('hide');
                    break;
                case 'warning':
                    okAlert(data.title, data.msg, data.status);
                    break;
                case 'error':
                    toastAlert(data.msg, data.status);
                    reloadProcessesKeepPage();
                    $('#addModal').modal('hide');
                    break;
                default:
                    break;
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addModal');
        });
    }
});
/**
 * Set data process in filters
 */
function setDataFilters(data, sectorIdCorporate){
    if (ID_PROFILE_TYPE == profilesType.ownerGlobal || ID_PROFILE_TYPE == profilesType.ownerOperative) {
        return setCorporatesActive(data.id_customer, sectorIdCorporate)
    }
    else {
        return new Promise((resolve, reject) => {
            resolve(true);
        });
    }
}
/**
 * service get data process
 */
function getDataProcessService(idProcess){
    return new Promise((resolve, reject) =>{
        $.get(`/processes/${idProcess}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
        },
        data => {
            resolve(data)
        })
        .fail(e => {
            reject(e.statusText);
        });
    })
}
/**
 * Open modal edit form
 */
function openEditProcesses(processes, idProcess) {
    idProcessCurrent = idProcess;
    document.querySelector('#updateProcessesForm').reset();
    $('#updateProcessesForm').validate().resetForm();
    $('#updateProcessesForm').find(".error").removeClass("error");
    $('.selectpicker').selectpicker('deselectAll');
    document.querySelector('#titleEdit').innerHTML = `Editar: "${processes}"`;
    // get processes
    $('.loading').removeClass('d-none')
    getDataProcessService(idProcess)
    .then(data => {
        document.querySelector('#u-processes').value = data.process[0].audit_processes;
        document.querySelector('#u-idScope').value = data.process[0].id_scope;
        showField(data.process[0].id_scope, '#u-specification');
        document.querySelector('#u-specification').value = data.process[0].specification_scope;
        document.querySelector('#u-idCustomer').value = data.process[0].id_customer;
        document.querySelector('#u-risk').checked = (data.process[0].evaluate_risk == 1) ? true : false;
        setDataFilters(data.process[0], '#u-idCorporate')
        .then(res => {
            document.querySelector('#u-idCorporate').value = data.process[0].id_corporate;
            return getLeaders(data.process[0].id_corporate, '#u-idLeader', '#u-auditors')
        })
        .then(res => {
            document.querySelector('#u-idLeader').value = data.leader[0].id_user;
            return getAuditors(data.leader[0].id_user, '#u-auditors')
        })
        .then(res => setAuditors(data.auditors, '#u-auditors') )
        .then(res => setAspects(data.aspects, '#u-aspects') )
        .then(res => {
            $('.loading').addClass('d-none')
            $('#editModal').modal({backdrop:'static', keyboard: false});
        })
        .catch(e => {
            $('.loading').addClass('d-none')
            toastAlert('Algo salio mal al cargar información', 'error');
        });
    })
    .catch(e => {
        $('.loading').addClass('d-none')
        toastAlert(e, 'error');
    });
}
/**
 * Delete data show
 */
function removeDataShow(){
    $('.filedsShow').html('');
    showField(1, '#specification')
    $('#tableShowAuditors tbody').find('tr').remove()
    $('#tableShowAspects tbody').find('tr').remove()
}
/**
 * show data process
 */
function showProcess(processes, idProcess){
    $('.loading').removeClass('d-none')
    removeDataShow();
    getDataProcessService(idProcess)
    .then(data => {
        document.querySelector('#titleShow').innerHTML = `Auditoría: ${processes}`
        document.querySelector('#customer').innerHTML = data.process[0].cust_trademark;
        document.querySelector('#corporate').innerHTML = data.process[0].corp_tradename;
        document.querySelector('#process').innerHTML = data.process[0].audit_processes;
        document.querySelector('#scope').innerHTML = data.process[0].scope;
        showField(data.process[0].id_scope, '#specification')
        document.querySelector('#specification').innerHTML = data.process[0].specification_scope;
        document.querySelector('#risk').innerHTML = (data.process[0].evaluate_risk == 1) ? 'Si' : 'No';
        let auditors = '';
        if (data.allAuditors.length > 0) {
            data.allAuditors.forEach(e => {
                auditors += `
                <tr>
                    <td class="text-center">${ (e.leader === 1) ? 'Auditor Lider' : 'Auditor' }</td>
                    <td class="text-center">${ e.complete_name }</td>
                <tr>`
            });
        }
        else {
            auditors += `
                <tr>
                    <td colspan="2" class="text-center">Sin Auditores</td>
                <tr>`
        }
        $('#tableShowAuditors tbody').append(auditors);
        let aspects = '';
        if (data.aspects.length > 0) {
            data.aspects.forEach(e => {
                aspects += `
                <tr>
                    <td class="text-center">${ e.matter }</td>
                    <td class="text-center">${ e.aspect }</td>
                <tr>`
            });
        } 
        else {
            aspects += `
                <tr>
                    <td colspan="2" class="text-center">Sin aspectos</td>
                <tr>`
        }
        $('#tableShowAspects tbody').append(aspects);
        $('.loading').addClass('d-none')
        $('#showModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none')
        toastAlert(e, 'error');
    });
}
/**
 * Handler to submit update Processes form 
 */
$('#updateProcessesForm').submit( (event) => {
    event.preventDefault();
    if($('#updateProcessesForm').valid()) {
        showLoading('#editModal');
        //handler notificaction
        $.post('/processes/update', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idProcess: idProcessCurrent,
            idCustomer: document.querySelector('#u-idCustomer').value,
            idCorporate: document.querySelector('#u-idCorporate').value,
            process: document.querySelector('#u-processes').value,
            idScope: document.querySelector('#u-idScope').value,
            specification: document.querySelector('#u-specification').value,
            idLeader: document.querySelector('#u-idLeader').value,
            setRisk: document.querySelector('#u-risk').checked,
            auditors: $('#u-auditors').val(),
            aspects: $('#u-aspects').val()
        },
        (data) => {
            switch (data.status) {
                case 'success':
                    toastAlert(data.msg, data.status);
                    reloadProcessesKeepPage();
                    showLoading('#editModal');
                    $('#editModal').modal('hide');
                    break;
                case 'warning':
                    okAlert(data.title, data.msg, data.status);
                    showLoading('#editModal');
                    break;
                case 'error':
                    toastAlert(data.msg, data.status);
                    reloadProcessesKeepPage();
                    showLoading('#editModal');
                    break;
                default:
                    break;
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editModal');
        });
    }
});
/**
 * Delete Processes
 */
function deleteProcesses(processes, idElement) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${processes}"?`,
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
            $.post('/processes/delete',
            {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idProcesses: idElement
            },
            (data) => {
                toastAlert("Registro Eliminado", 'success');
                reloadProcessesKeepPage();
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
            });
        }
    });
}
/**
 * Open external link aplicability
 */
function openAplicability(idAplicabilityRegister) {
    $('.loading').removeClass('d-none');
    let id = btoa(idAplicabilityRegister)
    window.location.href = `/aplicability/register/${id}`;
}
/**
 * Open external link audit
 */
function openAudit(idAuditRegister) {
    $('.loading').removeClass('d-none');
    let id = btoa(idAuditRegister);
    window.location.href = `/audit/register/${id}`;
}
</script>