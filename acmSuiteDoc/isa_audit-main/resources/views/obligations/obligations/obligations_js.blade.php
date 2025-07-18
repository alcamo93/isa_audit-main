<script>
$(document).ready(() => {
    setFormValidation('#obligationsForm');
    setFormValidation('#asignedDateForm');
    setFormValidation('#asignedForm');
});
activeMenu(14, 'Obligaciones');
/** 
 * Obligations dataTable
 */ 
const obligationsTable = $('#obligationsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/obligations',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.filterIdCustomer = document.querySelector('#filter-idCustomer').value,
            data.filterIdCorporate = document.querySelector('#filter-idCorporate').value,
            data.filterIdAuditProcess = document.querySelector('#filter-idAuditProcess').value,
            data.filterObligation = document.querySelector('#filterObligation').value,
            data.filterStatus = document.querySelector('#filterStatus').value,
            data.filterPeriod = document.querySelector('#filterPeriod').value,
            data.filterIdCondition = document.querySelector('#filterIdCondition').value,
            data.idUser = USER;
        }
    },
    columnDefs: [
        {
            data: data => {
                return data.process.audit_processes;
            },
            className: 'text-center',
            orderable: true,
            targets: 0
        },
        {
            data: data => {
                const limit = 18;
                const { title } = data;
                const textTruncate = (title.length > limit) ? `${title.substr(0,limit)}…` : title;
                const linkObligation = `
                    <a class="btn btn-link go-to-process"
                        data-toggle="tooltip" 
                        title="Ver obligación completa"
                        onclick="showText('Obligación', '${title}')">
                        ${textTruncate}
                    </a>`;
                return linkObligation
            },
            className: 'text-center truncate',
            orderable: true,
            targets: 1
        },
        {
            data: data => {
                return data.type.obligation_type;
            },
            className: 'text-center',
            orderable: false,
            targets: 2
        },
        {
            data: data => {
                let btnCheck = 'N/A';
                if(data.count_obligaction_requirement != 0){
                    btnCheck =`<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver Requerimientos" 
                        onclick"openObRequirementsModal(${data.id_obligation})">
                        <i class="fa fa-list fa-lg"></i>
                    </a>`;
                }
                return btnCheck;
            },
            className: 'text-center',
            orderable: false,
            targets: 3
        },
        {
            data: data => {
                let date = null
                if (data.init_date != null) {
                    date = formatDate(data.init_date, 'date');
                }
                const linkDate = `<a class="btn btn-link btn-xs" data-toggle="tooltip" title="Establecer fecha" 
                        onclick="setDate('${data.title}', ${data.id_obligation}, ${data.id_period})">
                        <i class="fa fa-calendar fa-lg"></i>
                        <div>${date || 'Sin fecha definida'}</div>
                    </a>`;
                return linkDate
            },
            className: 'td-actions text-center',
            orderable: false,
            targets: 4
        },
        {
            data: data => {
                let date = null
                if (data.renewal_date != null) {
                    date = formatDate(data.renewal_date, 'date');
                }
                const linkDate = `<a class="btn btn-link">
                    ${date || 'Sin fecha definida'}
                    </a>`;
                return linkDate
            },
            className: 'td-actions text-center',
            orderable: false,
            targets: 5
        },
        {
            data: data => {
                let date = null
                if (data.last_renewal_date != null) {
                    date = formatDate(data.last_renewal_date, 'date');
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
                const index = data.id_status || 0; 
                const {color, status} = statusObligation[index];
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
                    btn = `
                        <a class="btn btn-link go-to-process" 
                            onclick="asignedUser(${data.id_obligation}, ${data.id_audit_processes})"
                            data-toggle="tooltip" 
                            title="Abrir sección Resposables"
                            <div>
                                <img width="50px" class="avatar" src="/assets/img/faces/${img}">
                            </div>
                            <div>${name}</div>
                        </a>`;
                }
                else {
                    btn = `
                        <a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" 
                            title="Asignar Responsables" 
                            onclick="asignedUser(${data.id_obligation}, ${data.id_audit_processes})">
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
                const idFile = (data.file) ? data.file.id_file : null;
                const btnFile = `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Documentos" 
                        onclick="setFile('${data.title}', ${data.id_obligation}, ${data.id_status}, ${idFile})">
                        <i class="fa fa-file-pdf-o fa-lg"></i> 
                    </a>`;
                const btnComment = `<a class="btn btn-primary btn-link btn-xs" data-toggle="tooltip" title="Agregar comentario" 
                        onclick="addCommnet('${data.title}', ${data.id_obligation})">
                        <i class="fa fa-comments fa-lg"></i>
                    </a>`;
                const btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                        onclick="openObligationsModal(${data.id_obligation})">
                        <i class="fa fa-edit fa-lg"></i>
                    </a>`:'';
                const btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                        onclick="deleteObligation(${data.id_obligation})">
                        <i class="fa fa-times fa-lg"></i>
                    </a>`:'';
                return btnComment+btnFile+btnEdit+btnDelete
            },
            className: 'td-actions text-center',
            targets: 9
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
})
/**
 * Reload select basis Datatables
 */
const reloadObligations = () => { obligationsTable.ajax.reload() }
const reloadObligationsKeepPage = () => { obligationsTable.ajax.reload(null, false) }
/**
 * Set data by profile
 */
function setDataProfile(process) {
    return new Promise((resolve, reject) => {
        switch (ID_PROFILE_TYPE) {
            case 1:
            case 2:
                return setCorporates(process.id_customer, '#idCorporate')
                .then(data => {
                    document.querySelector('#idCustomer').value = process.id_customer;
                    return setAuditProcess(process.id_corporate, '#idAuditProcess') 
                })
                .then(data => {
                    document.querySelector('#idCorporate').value = process.id_corporate;
                    document.querySelector('#idAuditProcess').value = process.id_audit_processes;
                    resolve(true);
                })
                .catch(e => reject(false));
                break;
            case 3:
                return setAuditProcess(process.id_corporate, '#idAuditProcess')
                .then(data => {
                    document.querySelector('#idCustomer').value = process.id_customer;
                    document.querySelector('#idCorporate').value = process.id_corporate;
                    document.querySelector('#idAuditProcess').value = process.id_audit_processes;
                    resolve(true);
                })
                .catch(e => reject(false));
                break;
            case 4:
            case 5:
                document.querySelector('#idCustomer').value = process.id_customer;
                document.querySelector('#idCorporate').value = process.id_corporate;
                document.querySelector('#idAuditProcess').value = process.id_audit_processes;
                resolve(true);
                break;
        }
    });
}
/**
 * Open obligations modal
 */
function openObligationsModal(idObligation = null ) {
    cleanForm('#obligationsForm');
    const form = document.querySelector('#obligationsForm');
    const titleModal = document.querySelector('#obligationsModalTitle');
    const buttonModal = document.querySelector('#btnSubmitObligation');
    currentAP.idObligation = idObligation;
    if(idObligation) {
        $('.loading').removeClass('d-none')
        getDataObligationService()
        .then(data => {
            form.setAttribute('action', '/obligations/update')
            titleModal.innerHTML = 'Editar obligación';
            buttonModal.innerHTML = 'Editar';
            // set data in form
            return setDataProfile(data.process)
            .then(res => {
                document.querySelector('#obligation').value = data.obligation;
                document.querySelector('#title').value = data.title;
                document.querySelector('#idPeriod').value = data.id_period;
                document.querySelector('#idObType').value = data.id_obligation_type;
                document.querySelector('#idCondition').value = data.id_condition;
                $('.loading').addClass('d-none')
                $('#obligationsModal').modal({backdrop:'static', keyboard: false});
            })
        })
        .catch(e => {
            $('.loading').addClass('d-none')
            toastAlert(e, 'error');
        });
    }
    else {
        form.setAttribute('action', '/obligations/set')
        titleModal.innerHTML = 'Agregar Obligación';
        buttonModal.innerHTML = 'Registrar';
        $('#obligationsModal').modal({backdrop:'static', keyboard: false});
    }
}

/**
 * Set obligation
 */
$('#btnSubmitObligation').click(e => {
    e.preventDefault()
    if($('#obligationsForm').valid()){
        const action = document.querySelector('#obligationsForm').getAttribute('action')
        showLoading('#obligationsModal')
        $.post(action, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idObligation: currentAP.idObligation,
            idCustomer: document.querySelector('#idCustomer').value,
            idCorporate: document.querySelector('#idCorporate').value,
            idAuditProcess: document.querySelector('#idAuditProcess').value,
            obligation: document.querySelector('#obligation').value,
            title: document.querySelector('#title').value,
            idPeriod: document.querySelector('#idPeriod').value,
            idCondition: document.querySelector('#idCondition').value,
            idObligationType: document.querySelector('#idObType').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#obligationsModal')
            reloadObligations()
            if(data.status == 'success') $('#obligationsModal').modal('hide') 
        })
        .fail(e => {
            toastAlert(e.statusText, 'error');
            showLoading('#obligationsModal')
        })
    }
});

/**
 * Delete question
 */
function deleteObligation(idElement) {
    Swal.fire({
        title: `¿Estas seguro de eliminar esta obligación?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    })
    .then((result) => {
        if (result.value) {
            $('.loading').removeClass('d-none')
            // send to server
            $.post('/obligations/delete', {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idObligation: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success') reloadObligations();
            })
            .fail(e => {
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            })
        }
    });
}
/**
 * Acepted documentation
 */
 function completeObligation(idObligation, name){
    Swal.fire({
        title: `¿Cómo quieres marcar la obligación '${name}'?`,
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
            // 23: Vigente
            let idStatus = 23;
            setDocumentStatus(idObligation, idStatus);  
        }
        else if (result.dismiss == 'cancel') {
            // 24: Rechazado
            let idStatus = 24;
            setDocumentStatus(idObligation, idStatus);  
        }
    });
 }
/**
 * Action obout status
 */
 function setDocumentStatus(idObligation, idStatus){
     $('.loading').removeClass('d-none')
     $.post('/obligations/complete',
    {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        idObligation: idObligation,
        idStatus: idStatus
    },
    (data) => {
        $('.loading').addClass('d-none')
        toastAlert(data.msg, data.status);
        if (data.status == 'success') {
            reloadObligations();
        }
    });
 }
 /**
 * Set answer request permit real close date modal
 */
function setPermitAdmin(idAObligation, name, permit, user){
    Swal.fire({                 
        title: `La Fecha de Renovación expiró el usuario ${user} ha solicitado autorización para habilitar la Fecha de Renovación Real`,
        text: `¿Desea habilitar la Fecha de Renovación Real para ${name}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#87CB16',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Autorizar',
        cancelButtonText: 'No Autorizar',
        allowOutsideClick: false,
        showCloseButton: true
    }).then((result) => {
        if (result.value) {
            setPermit(idAObligation, 1);
        }
        else if (result.dismiss == 'cancel') {
            setPermit(idAObligation, 0);  
        }
    });
}
/**
 * set value permit
 */
function setPermit(idObligation, permit){
    $.post('/obligations/request', {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        idObligation: idObligation,
        permit: permit
    },
    (data) => {
        toastAlert(data.msg, data.status);
        if (data.status == 'success') {
            reloadObligations();
        }
    });
}
/*************** Show long text AP ***************/
/**
 * Show text 
 */
function showText(title, bodyText){
    document.querySelector('#showAPTitle').innerHTML =  `<b>${title}</b>`;
    document.querySelector('#showAPText').innerHTML = '';
    let text = `<p class="text-justify">${bodyText}</p>`;
    $('#showAPText').append(text);
    $('#showAPModal').modal({backdrop:'static', keyboard: false});
}
</script>