<script>
/*************************** Aspects to Aplicability ***************************/

/**
 * Get Aspects by matter
 */
function setAspectsToAplicability() {
    document.querySelector('#customerTitle').innerHTML = currentAR.customer;
    document.querySelector('#corporateTitle').innerHTML = currentAR.corporate;
    $('.main-register').addClass('d-none');
    $('.loading').removeClass('d-none');
    getContractMatters()
    .then(()=>{
        reloadaspectsRegister();
        $('.loading').addClass('d-none');
        $('.matters-list-card').removeClass('d-none');
    })
    .catch(()=>{
        $('.loading').addClass('d-none');
        $('.matters-list-card').removeClass('d-none');
        okAlert('Sin Materias para evaluar', 'Todas las Materias de la Aplicabilidad fueron eliminadas', 'error');
    });
}
/**
 * Show buttons
 */
function optionButtons(){
    switch (currentStatus.aplicability.currentStatus) {
        case CLASSIFIED:
            $('#finishAplicability').removeClass('d-none');
            break;
        case FINISHED:
            $('#finishAplicability').addClass('d-none');
            break;
    }
}
/**
 * set status in progress bar
 */
function setProgressBarByMatter(){
    return new Promise((resolve, reject) => {
        const idContractMatter = parseInt(document.querySelector('#idContractMatter').value);
        if (idContractMatter != 0) {
            const matterIndex = currentStatus.matters.findIndex( e => e.idContractMatter === idContractMatter );
            if (matterIndex == -1) reject(false);
            const { percent, matter, classfied, total, statusMatter } = currentStatus.matters[matterIndex];
            $('#progressBarMatter').css({width: `${percent}%`});
            document.querySelector('#barPercentMatter').innerHTML = `${percent}%`;
            document.querySelector('#matterTitle').innerHTML = matter;
            document.querySelector('#advanceTitle').innerHTML = `${classfied} / ${total}`;
            document.querySelector('#statusTitle').innerHTML = `${statusMatter}`;
            $('.progressMatterArea').removeClass('d-none');
            resolve(true);
        }
        else {
            $('.progressMatterArea').addClass('d-none');
            resolve(true);
        }
    });
}
/**
 * Get aplicability aspects by matter
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
        url: '/aplicability/resgisters/aspects',
        type: 'POST',
        data: data => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.idAuditProcess = currentAR.idAuditProcess,
            data.idContractMatter = document.querySelector('#idContractMatter').value,
            data.filterIdStatus = document.querySelector('#filterIdStatusAspect').value
        }
    },
    columns: [
        { data: 'matter', className: 'text-center', orderable : true },
        { data: 'aspect', className: 'text-center', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'application_type', className: 'text-center', orderable : true },
        { data: 'id_aspect', className: 'text-center td-actions', orderable : false, visible: true }
    ],
    columnDefs: [
        {
            render: ( data, type, row ) => {
                let color = '';
                switch (data) {
                    case 'Sin clasificar':
                        color = 'warning';
                        break;
                    case 'Clasificado':
                        color = 'success';
                        break;
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
            render: (data, type, row) => {
                // let action = '';
                // if(row.id_status != FINISHED){
                    let action = `"openQuiz(${data}, '${row.matter}', '${row.aspect}', ${row.id_contract_aspect}, 
                        ${row.id_status}, ${row.id_contract_matter}, ${row.id_aplicability_register}, ${row.form_id})"`;
                // }else{
                //     action = `"okAlert('Aspecto Finalizado', 'Una vez finalizado la aplicabilidad no es posible editar', 'info')"`;
                // }
                let btnQuiz = (MODIFY) ? `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Evaluación" 
                        onclick=${action}>
                        <i class="fa fa-check-square-o fa-lg"></i>
                    </button>`:'';
                let btnDelete = (DELETE) ? `<button class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar aspecto" 
                    onclick="deleteAspect('${row.aspect}', ${row.id_contract_matter}, ${row.id_contract_aspect}, ${row.id_aplicability_register})">
                    <i class="fa fa-times fa-lg"></i>
                </button>`:'';
                return btnQuiz+btnDelete;
            },
            targets: 4
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
function reloadaspectsRegister() { 
    progressMatter()
    .then(res => setProgressBarByMatter() )
    .then(res => {
        optionButtons();
        aspectsRegisterTable.ajax.reload(null, true); 
    })
    .catch(e => {
        toastAlert('No se pudo obtener estatus', 'error');
        aspectsRegisterTable.ajax.reload(null, true); 
    })
}
/**
 * close aspects lists
 */
function closeAplicabilityMatters(){
    $('.loading').removeClass('d-none');
    window.location.href = `/v2/process/view`;
}
/**
 * Delete Aspect
 */
function deleteAspect(aspectName, idContractMatter, idContractAspect, idAplicabilityRegister){
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
            // send to server
            $.post('/aplicability/resgisters/aspect/delete', {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idContractMatter: idContractMatter,
                idContractAspect: idContractAspect
            },
            data => {
                $('.loading').addClass('d-none');
                toastAlert(data.msg, data.status);
                getContractMatters(idAplicabilityRegister)
                .then(()=>{
                    reloadaspectsRegister();
                })
                .catch(()=>{
                    toastAlert('No se pudo obtener las materias', 'error');
                });
            })
            .fail(e=> {
                $('.loading').addClass('d-none');
                toastAlert(e.statusText, 'error');
            });
        }
    });
}
/**
 * Set aplicability in Audit
 */
function setInAudit(){
    Swal.fire({
        title: '¿Estás seguro de marcar como finalizado?',
        text: "Una vez finalizado no se podra editar nada de aplicabilidad",
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
            setInAuditService()
            .then(data => {
                $('.loading').addClass('d-none');
                if (data.status == 'success' || data.status == 'warning') {
                    okAlert(data.title, data.msg, data.status);
                    reloadaspectsRegister();
                }else{
                    toastAlert(data.msg, data.status);
                }
            })
            .catch(e=> {
                $('.loading').addClass('d-none');
                toastAlert(e, 'error');
            })
        }
    });
}
/**
 * Create aplicability report
 */
function reportAplicability(){
    window.open(`/v2/applicability/register/${currentAR.idAplicabilityRegister}/report/answers`);
}
function reportResultsAplicability() {
    window.open(`/v2/applicability/register/${currentAR.idAplicabilityRegister}/report/results`)
}
</script>
