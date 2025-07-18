<script>
$(document).ready( () => {
    setFormValidation('#obligationsForm');
    setFormValidation('#asignedDateForm');
});
activeMenu(13, 'Obligaciones');

const obligations = {
    idActionRegister : null,
    data: [],
    selected: null
}
const idUser = '{{ $idUser }}'
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
            data.filterIdCondition = document.querySelector('#filterIdCondition').value,
            data.idUser = idUser
        }
    },
    columns: [
        { data: 'title', className: 'text-center', orderable : true },
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
                let color = '';
                let status = '';
                switch (data) {
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
            targets: 4
        },
        {
            render: (data, type, row) => {
                let btnComments = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar Comenatarios"
                        href="javascript:openComments(null, ${data}, 'view-obligation')">
                        <i class="fa fa-comments-o fa-lg"></i>
                    </a>`;
                let files = `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Documentos" 
                        href="javascript:openFiles(null, null, ${row.id_obligation}, null, 'obligations')">
                        <i class="fa fa-file-pdf-o fa-lg"></i>
                    </a>`;
                let btnCheck = ( (MODIFY) && (idUser != row.id_user_asigned) && ((row.id_status == 22) || (row.id_status == 24)) ) ? `<a class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Aceptar documentación" 
                        href="javascript:completeObligation(${row.id_obligation}, '${row.title}')">
                        <i class="fa fa-check-square la-lg"></i>
                    </a>` : '';
                let btnEdit = ''
                let btnDate = ''
                let btnRequest = '';
                let btnRemainder = '';
                if ( MODIFY && (row.id_user_asigned == idUser) ) {
                    btnDate = ( row.complete_name != null ) ? `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Establecer Fechas" 
                            href="javascript:asignedDate(${row.id_obligation}, '${row.title}')">
                            <i class="fa fa-calendar-check-o fa-lg"></i>
                        </a>` : '';
                    btnEdit = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                        href="javascript:openObligationsModal(${data})">
                        <i class="fa fa-edit fa-lg"></i>
                    </a>`;
                    btnRequest = ( (TODAY > row.renewal_date) && (row.permit == 0) && (row.last_renewal_date == null) ) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Solicitar Fecha de Renovación Real" 
                            href="javascript:requestPermit(${row.id_obligation}, '${row.title}')"> <i class="fa fa-calendar-plus-o fa-lg"></i>
                        </a>`:'';
                    if ( (row.renewal_date != null) && (TODAY <= row.renewal_date) ) {
                            btnRemainder = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Recordatorios" 
                                    href="javascript:reminderCloseDate('${row.title}', ${row.id_obligation})"> <i class="fa fa-bell-o fa-lg"></i>
                                </a>`;
                        }
                    if ( (row.last_renewal_date != null) && (TODAY <= row.last_renewal_date)) {
                        btnRemainder = `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Recordatorios" 
                                href="javascript:reminderRealCloseDate('${row.title}', ${row.id_obligation})"> <i class="fa fa-bell-o fa-lg"></i>
                            </a>`;
                    }
                }
                let btnDelete = ''
                if ( DELETE && (row.id_user == idUser) ) {
                    btnDelete = `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                        href="javascript:deleteObligation(${data})">
                        <i class="fa fa-times fa-lg"></i>
                    </a>`;
                }
                return btnComments+files+btnCheck+btnDate+btnRequest+btnRemainder+btnEdit+btnDelete
            },
            targets: 6
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
    obligationsTable.ajax.reload() 
}
const reloadObligationsKeepPage = () =>
{ 
    obligations.data = null
    obligations.data = []
    obligationsTable.ajax.reload(null, false) 
}
/**
 * Open obligations modal
 */
function openObligationsModal (idObligation = null ) {
    document.querySelector('#obligationsForm').reset();
    if(idObligation){
        let index = obligations.data.findIndex( o => o.id_obligation === idObligation ) 
        $('#obligationsForm').attr('action', '/obligations/update')
        $('#obligationsModalTitle').html('Editar obligación')
        $('#btnSubmitObligation').html('Editar')
        $('#obligation').attr('idObligation', obligations.data[index].id_obligation)
        $('#obligation').val(obligations.data[index].obligation)
        $('#title').val(obligations.data[index].title)
        $('#idPeriod').val(obligations.data[index].id_period)
        $('#idCondition').val(obligations.data[index].id_condition)
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
            $.post('/obligations/delete',
                {
                    '_token':'{{ csrf_token() }}',
                    idObligation: idElement
                },
                (data) => {
                    toastAlert(data.msg, data.status);
                    $('.loading').addClass('d-none')
                    if(data.status == 'success'){
                        reloadObligationsKeepPage();
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
 *  Obligation status change
 */
function changeObligationStatus(status, idObligation)
{
    $('.loading').removeClass('d-none')
    $.post('/obligations/status', {
        '_token': '{{ csrf_token() }}',
        status : status,
        idObligation : idObligation
    },
    (data) => {
        toastAlert(data.msg, data.status);
        reloadObligationsKeepPage()
        $('.loading').addClass('d-none')
    });
}
/**
* Assigned status
*/
function asignedDate(idObligation, title){
    document.querySelector('#s-initDate').disabled = false;
    document.querySelector('#s-renewalDate').disabled = false;
    $('.requestClass').addClass('d-none');
    document.querySelector('#dateIdObligation').value = idObligation;
    $.get('/obligations/data/get', {
        '_token': '{{ csrf_token() }}',
        idObligation: idObligation
    },
    (data) => {
        // data
        document.querySelector('#s-initDate').value = data.obligation.initDate;
        document.querySelector('#idPeriodCalc').value = data.obligation.id_period;
        document.querySelector('#updatePeriodText').innerHTML = data.obligation.period;
        // config
        if ( (data.obligation.renewalDate === null) || (TODAY <= data.obligation.renewalDate) ) {
            document.querySelector('#s-renewalDate').min = data.limits.initDate;
            document.querySelector('#s-renewalDate').max = data.limits.renewalDate;
            document.querySelector('#s-renewalDate').value = data.obligation.renewalDate;
        }
        else if ( (TODAY > data.obligation.renewalDate) && (data.obligation.permit == 1) ) {
            document.querySelector('#s-renewalDate').min = data.limits.initDate;
            document.querySelector('#s-renewalDate').max = data.limits.renewalDate;
            document.querySelector('#s-renewalDate').value = data.obligation.renewalDate;
            document.querySelector('#s-renewalDate').disabled = true;
        }
        else if ( (TODAY > data.obligation.renewalDate) && (data.obligation.permit == 1) ) {
            document.querySelector('#s-lastRenewalDate').min = data.limits.initDate;
            document.querySelector('#s-lastRenewalDate').max = data.limits.lastRenewalDate;
            document.querySelector('#s-lastRenewalDate').value = data.obligation.lastRenewalDate;
            document.querySelector('#s-renewalDate').value = data.obligation.renewalDate;
            document.querySelector('#s-renewalDate').disabled = true;
            $('#rowRealCloseDate').removeClass('d-none');
        }
        else if ( (TODAY > data.obligation.lastRenewalDate) && (data.obligation.permit == 1) ) {
            document.querySelector('#s-lastRenewalDate').min = TODAY;
            document.querySelector('#s-lastRenewalDate').value = data.obligation.lastRenewalDate;
            document.querySelector('#s-renewalDate').value = data.obligation.renewalDate;
            document.querySelector('#s-renewalDate').disabled = true;
            $('#rowRealCloseDate').removeClass('d-none');
        }
        // validate dates
        if (TODAY > data.obligation.renewalDate) {
            document.querySelector('#s-initDate').disabled = true;
            document.querySelector('#s-renewalDate').disabled = true;
            document.querySelector('#s-lastRenewalDate').min = TODAY;
            document.querySelector('#s-lastRenewalDate').max = '';
            if ( (TODAY > data.obligation.renewalDate) && (data.obligation.lastRenewalDate == null) && (data.obligation.permit != 1) ) {
                if (data.obligation.permit == 0) {
                    $('.requestClass').removeClass('d-none');
                }
                else{
                    $('.pendientClass').removeClass('d-none');
                }
            }
        }
    });
    document.querySelector('#asignedDateForm').reset();
    $('#asignedDateForm').validate().resetForm();
    $('#asignedDateForm').find(".error").removeClass("error");
    $('#asignedDateModal').modal({backdrop:'static', keyboard: false});
    document.querySelector('#asignedDateTitle').innerHTML = `Asignación de Fechas: ${title}`;
}
/**
 * Set request permit
 */
function requestPermit(idObligation, name){
    Swal.fire({
        title: `La Fecha de renovación expiró`,
        text: `¿Desea solicitar autorización para habiliatar Fecha de renovación Real en ${name}?`,
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Solicitar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            setPermit(idObligation, 2);
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
            reloadObligationsKeepPage();
        }
    });
}
/**
 * Calculate limits based on init date
 */
function calculateDates(initDate){
    $.get('/obligations/date/calc', {
        '_token': '{{ csrf_token() }}',
        initDate: initDate,
        idPeriod: document.querySelector('#idPeriodCalc').value
    },
    (data) => {
        // date min
        document.querySelector('#s-renewalDate').min = data.initDate;
        document.querySelector('#s-lastRenewalDate').min = data.initLastReal;
        // date max
        document.querySelector('#s-renewalDate').max = data.renewalDate;
        document.querySelector('#s-lastRenewalDate').max = data.lastRenewalDate;
    });
}
/**
* Handler to submit assigned user
*/
$('#asignedDateForm').submit( (event) => {
    event.preventDefault() 
    if($('#asignedDateForm').valid()) {
        showLoading('#asignedDateModal')
        //handler notificaction
        $.post('{{ asset('/obligations/date/set') }}', {
            '_token': '{{ csrf_token() }}',
            initDate: document.querySelector('#s-initDate').value,
            lastRenewalDate: document.querySelector('#s-lastRenewalDate').value,
            renewalDate: document.querySelector('#s-renewalDate').value,
            idObligation: document.querySelector('#dateIdObligation').value
        },
        (data) => {
            showLoading('#asignedDateModal')
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                reloadObligationsKeepPage();
                $('#asignedDateModal').modal('hide');
            }
        });
    }
});

/*************** Reminder ***************/

/**
 * Show reminders close date
 */
function reminderCloseDate(title, idObligation){
    document.querySelector('#remindersCloseForm').reset();
    $('#remindersCloseForm').validate().resetForm();
    $('#remindersCloseForm').find(".error").removeClass("error");
    document.querySelector("#textClose").innerHTML = 'Fecha de Renovación';
    document.querySelector("#textCloseFooter").innerHTML = 'Fecha de Renovación';
    // Data
    $.get('/obligations/data/reminders', {
        '_token': '{{ csrf_token() }}',
        idActionPlan: null,
        idObligation: idObligation,
        idTask: null,
        typeDate: 0
    },
    (data) => {
        flatpickr('#s-reminder', {
            locale: lenguageFlatpickr,
            allowInput: false,
            clickOpens: false,
            inline: true,
            position: 'below',
            monthSelectorType: 'static',
            mode: 'multiple',
            dateFormat: 'Y-m-d',
            minDate: 'today',
            maxDate: data.obligation.renewalDate,
            defaultDate: data.dates,
            onChange: (selectedDates, dateStr, instance) => {
                setDatesReminder(null, idObligation, null, selectedDates, 0);
            },
        });
    });
    document.querySelector('#remindersCloseTitle').innerHTML = `Obligación: ${title}`;
    $('#remindersCloseModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Show reminders real close date
 */
function reminderRealCloseDate(title, idObligation){
    document.querySelector('#remindersRealCloseForm').reset();
    $('#remindersRealCloseForm').validate().resetForm();
    $('#remindersRealCloseForm').find(".error").removeClass("error");
    document.querySelector("#textRealClose").innerHTML = 'Fecha de Renoavción Real';
    document.querySelector("#textRealCloseFooter").innerHTML = 'Fecha de Renoavción Real';
    // Data
    $.get('/action/data/reminders', {
        '_token': '{{ csrf_token() }}',
        idActionPlan: null,
        idObligation: null,
        idTask: null,
        typeDate: 1
    },
    (data) => {
        flatpickr('#s-reminderReal', {
            locale: lenguageFlatpickr,
            allowInput: false,
            clickOpens: false,
            inline: true,
            position: 'below',
            monthSelectorType: 'static',
            mode: 'multiple',
            dateFormat: 'Y-m-d',
            minDate: 'today',
            maxDate: data.obligation.lastRenewalDate,
            defaultDate: data.dates,
            onChange: (selectedDates, dateStr, instance) => {
                setDatesReminder(idActionPlan, null, null, selectedDates, 1);
            },
        });
    });
    document.querySelector('#remindersRealCloseTitle').innerHTML = `Obligación: ${title}`;
    $('#remindersRealCloseModal').modal({backdrop:'static', keyboard: false});
}
</script>