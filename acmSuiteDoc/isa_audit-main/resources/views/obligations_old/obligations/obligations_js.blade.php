<script>
$(document).ready( () => {
    setFormValidation('#obligationsForm');
    setFormValidation('#asignedDateForm');
});
activeMenu(14, 'Obligaciones');

const obligations = {
    idActionRegister: null,
    idCorporate: null,
    data: null,
    selected: null
}
/**
 * Open Obligations 
 */
function openObligations(idActionRegister, idCorporate)
{
    obligations.idActionRegister = idActionRegister
    obligations.idCorporate = idCorporate
    $('.loading').removeClass('d-none')
    $('.contracts').addClass('d-none')
    $('body').addClass('sidebar-mini')
    reloadObligations()
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.obligations').removeClass('d-none')
    }, 1000)
}
/**
 * Close Obligations 
 */
function closeObligations()
{
    $('.loading').removeClass('d-none')
    $('.obligations').addClass('d-none')
    setTimeout(() => {
        obligations.idActionRegister = null
        obligations.idCorporate = null
        obligations.data = null
        reloadObligations()
        $('.loading').addClass('d-none')
        $('.contracts').removeClass('d-none')
    }, 1000)
}
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
            data._token = '{{ csrf_token() }}',
            data.idActionRegister = obligations.idActionRegister,
            data.filterObligation = document.querySelector('#filterObligation').value,
            data.filterStatus = document.querySelector('#filterStatus').value,
            data.filterPeriod = document.querySelector('#filterPeriod').value,
            data.filterIdCondition = document.querySelector('#filterIdCondition').value
        }
    },
    columns: [
        { data: 'title', className: 'text-center', orderable : true },
        { data: 'obligation_type', className: 'text-center', orderable : false },
        { data: 'id_ob_req', className: 'text-center', orderable : false },
        { data: 'init_date', className: 'text-center', orderable : false },
        { data: 'renewal_date', className: 'text-center', orderable : false },
        { data: 'last_renewal_date', className: 'text-center', orderable : false },
        { data: 'id_status', className: 'td-actions text-center', orderable : false },
        { data: 'complete_name', className: 'td-actions text-center', orderable : false },
        { data: 'id_obligation', className: 'td-actions text-center', orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                let btnCheck = '';
                if(row.id_ob_req != 0){
                    btnCheck =`<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver Requerimientos" 
                        href="javascript:openObRequirementsModal(${row.id_obligation})">
                        <i class="fa fa-list fa-lg"></i>
                    </a>`;
                }
                return btnCheck
            },
            targets: 2            
        },
        {
            render: (data, type, row) => {
                let color = '';
                let status = '';
                switch (row.id_status) {
                    case 20:
                        color = 'danger';
                        status = 'Sin Responsable';
                        break;
                    case 21:
                        color = 'warning';
                        status = 'Revisión';
                        break;
                    case 22:
                        color = 'danger';
                        status = 'Expirado';
                        break;
                    case 23:
                        color = 'success';   
                        status = 'Vigente';
                        break;
                    case 24:
                        color = 'danger';
                        status = 'Rechazado';
                        break;
                    default:
                        break;
                }
                return `<span class="badge badge-${color} text-white">${status}</span>`; 
            },
            targets: 6
        },

        {
            render: (data, type, row) => {
                // let btnFullView = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver obligación" href="javascript:obligationFullView(${row.id_user_asigned})"><i class="fa fa-eye fa-lg"></i></a>`;
                let btnUsers = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Usuario asignado" href="javascript:openUsers(${data}, ${row.id_user_asigned})">
                        <i class="fa fa-user-plus"></i>
                    </a>`;
                let btnComments = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Comentarios" href="javascript:openComments(null, ${data}, 'view-obligation')">
                        <i class="fa fa-comments-o fa-lg"></i>
                    </a>`;
                let files = `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Documentos" 
                        href="javascript:openFiles(null, null, ${row.id_obligation}, null, 'obligations')">
                        <i class="fa fa-file-pdf-o fa-lg"></i>
                    </a>`;
                let btnPermit = (row.permit == 2) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Habilitar Fecha de Cierre Real" 
                        href="javascript:setPermitAdmin(${row.id_obligation}, '${row.title}', ${row.permit}, '${row.complete_name}')"> <i class="fa fa-calendar-plus-o fa-lg"></i>
                    </a>`:''; 
                let btnEdit = '';
                let btnDelete = '';
                if(row.id_user == USER){
                    btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                            href="javascript:openObligationsModal(${data})">
                            <i class="fa fa-edit fa-lg"></i>
                        </a>`:'';
                    btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                            href="javascript:deleteObligation(${data})">
                            <i class="fa fa-times fa-lg"></i>
                        </a>`:'';
                }
                let btnCheck = '';
                if(row.id_user != USER){
                    btnCheck = ( (MODIFY) && (row.id_status == 22 || row.id_status == 24) ) ? `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Aceptar documentación" 
                        href="javascript:completeObligation(${row.id_obligation}, '${row.title}')">
                        <i class="fa fa-check-square la-lg"></i>
                    </a>` : '';
                }
                return btnUsers+btnPermit+btnComments+files+btnCheck+btnEdit+btnDelete
            },
            targets: 8            
        }
    ],
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
const reloadObligations = () =>
{ 
    obligations.data = null
    obligations.data = []
    obligationsTable.ajax.reload(null, false) 
}
/**
 * Open obligations modal
 */
function openObligationsModal(idObligation = null ) {
    document.querySelector('#obligationsForm').reset();
    if(idObligation){
        $.get(`{{asset('/obligations/get')}}`,
        {
            '_token':'{{ csrf_token() }}',
            idObligation: idObligation
        },
        (data) => {
            if (data.length > 0) {
                $('#obligationsForm').attr('action', '/obligations/update')
                $('#obligationsModalTitle').html('Editar obligación')
                $('#btnSubmitObligation').html('Editar')
                $('#obligation').attr('idObligation', data[0].id_obligation)
                $('#obligation').val(data[0].obligation) 
                $('#title').val(data[0].title) 
                $('#idPeriod').val(data[0].id_period)
                $('#idObType').val(data[0].id_obligation_type)
                $('#idCondition').val(data[0].id_condition)
            }
        }); 
    }
    else{
        $('#obligationsModalTitle').html('Agregar Obligación')
        $('#btnSubmitObligation').html('Registrar') 
        $('#obligationsForm').attr('action', '/obligations/set')
    }
    $('#obligationsForm').validate().resetForm();
    $('#obligationsForm').find(".error").removeClass("error");
    $('#obligationsModal').modal({backdrop:'static', keyboard: false});
}

/**
 * Set obligation
 */
$('#btnSubmitObligation').click((e)=>{
    e.preventDefault()
    if($('#obligationsForm').valid()){
        let action = $('#obligationsForm').attr('action')
        showLoading('#obligationsModal')
        $.post(
            action,
            {
                '_token': '{{ csrf_token() }}',
                idObligation: $('#obligation').attr('idObligation'), 
                obligation: $('#obligation').val(), 
                title: $('#title').val(), 
                idPeriod: $('#idPeriod').val(),
                idCondition: $('#idCondition').val(),
                idObType: $('#idObType').val(),
                idUserAsigned: obligations.idUser,
                idActionRegister: obligations.idActionRegister,
                
            },
            (data)=>{
                toastAlert(data.msg, data.status);
                showLoading('#obligationsModal')
                reloadObligations()
                reloadObligations()
                if(data.status == 'success') $('#obligationsModal').modal('hide') 
            }
        )
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#obligationsModal')
        })
    }
});

/**
 * Delete question
 */
function deleteObligation(idElement)
{
    Swal.fire({
        title: `¿Estas seguro de eliminar esta obligación?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $('.loading').removeClass('d-none')
            // send to server
            $.post('{{asset('/obligations/delete')}}',
                {
                    '_token':'{{ csrf_token() }}',
                    idObligation: idElement
                },
                (data) => {
                    toastAlert(data.msg, data.status);
                    $('.loading').addClass('d-none')
                    if(data.status == 'success'){
                        reloadObligations();
                    }
                }
            )
            .fail((e)=>{
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
     $.post('{{asset('/obligations/complete')}}',
    {
        '_token':'{{ csrf_token() }}',
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
    $.post('{{ asset('/obligations/request') }}', {
        '_token': '{{ csrf_token() }}',
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
</script>