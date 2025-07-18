<script>
$(document).ready( () => {
    setFormValidation('#periodsForm');
});
/*************** Active menu ***************/
activeMenu(8, 'Periodos de acción');
/**
 * periods datatables
 */
const periods = {
    data: []
}

const periodsTable = $('#periodsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/periods',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.filterName = document.querySelector('#filterName').value
        }
    },
    columns: [
        { data: 'period', className: 'text-center', orderable : true },
        { data: 'id_period', className: 'td-actions text-center', orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                let index = periods.data.findIndex( o => o.id_period === row.id_period )
                if( index < 0 ) periods.data.push(row)
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openPeriodsModel(${data})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deletePeriod(${data}, '${row.period}')">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return btnEdit+btnDelete;
            },
            targets: 1
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
 * Reload industries Datatables
 */
const reloadPeriods = () => { 
    periods.data = []
    periodsTable.ajax.reload() 
}
const reloadPeriodsKeepPage = () => { 
    periods.data = []
    periodsTable.ajax.reload(null, false) 
}
/**
 * Open modal add form
 */
function openPeriodsModel (idPeriod = null)
{
    document.querySelector('#periodsForm').reset();
    $('#periodsForm').validate().resetForm();
    $('#periodsForm').find(".error").removeClass("error");
    fillSelect('#days', 'd', 31)
    fillSelect('#months', 'm', 12)
    fillSelect('#years', 'y', 10)
    fillSelect('#days-r', 'd', 31)
    fillSelect('#months-r', 'm', 12)
    fillSelect('#years-r', 'y', 10)
    if(idPeriod){
        let index = periods.data.findIndex( o => o.id_period === idPeriod )
        $('#modalTitle').html('Editar periodo');
        $('#btnModalPeriods').html('Actualizar');
        $('#periodsForm').attr('action', '/catalogs/periods/update');
        $('#name').attr('idPeriod', idPeriod)
        $('#name').val(periods.data[index].period)
        $('#days').val(periods.data[index].lastDay)
        $('#months').val(periods.data[index].lastMonth)
        $('#years').val(periods.data[index].lastYear)
        $('#days-r').val(periods.data[index].lastRealDay)
        $('#months-r').val(periods.data[index].lastRealMonth)
        $('#years-r').val(periods.data[index].lastRealYear)
    }
    else {
        $('#modalTitle').html('Nuevo periodo');
        $('#btnModalPeriods').html('Registrar');
        $('#periodsForm').attr('action', '/catalogs/periods/set');
    }
    
    $('#periodsModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set industries form 
 */
$('#periodsForm').submit( (event) => {
    event.preventDefault()
    let action = $('#periodsForm').attr('action') 
    if($('#periodsForm').valid()) {
        if($('#days').val() || $('#months').val() || $('#years').val()){
            if($('#days-r').val() || $('#months-r').val() || $('#years-r').val()){
                if( ($('#days-r').val() >= $('#days').val()) && ($('#months-r').val() >= $('#months').val()) && ($('#years-r').val() >= $('#years').val()) ){
                    showLoading('#periodsModal')
                    //handler notificaction
                    $.post(action, {
                        '_token': '{{ csrf_token() }}',
                        idPeriod: document.querySelector('#name').getAttribute('idPeriod'),
                        period: $('#name').val(),
                        lastDay: $('#days').val(),
                        lastMonth: $('#months').val(),
                        lastYear: $('#years').val(),
                        lastRealDay: $('#days-r').val(),
                        lastRealMonth: $('#months-r').val(),
                        lastRealYear: $('#years-r').val()
                    },
                    (data) => {
                        toastAlert(data.msg, data.status);
                        showLoading('#periodsModal')
                        if(data.status == 'success'){
                            reloadPeriodsKeepPage();
                            $('#periodsModal').modal('hide');
                        }
                    })
                    .fail((e)=>{
                        toastAlert(e.statusText, 'error');
                        showLoading('#periodsModal')
                    })
                }
                else toastAlert('La fecha de término real debe ser mayor o igual a la fecha de término', 'error');
            }
            else toastAlert('La fecha de término real por lo menos debe tener un día de duración', 'error');
        }
        else toastAlert('El periodo por lo menos debe tener un día de duración', 'error');
    }
    
});

/**
 * Delete period
 */
function deletePeriod(idElement, Name) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${Name}"?`,
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
            $('.loading').removeClass('d-none')
            $.post('/catalogs/periods/delete',
            {
                '_token':'{{ csrf_token() }}',
                id_period: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadPeriodsKeepPage();
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            })
        }
    });
}
/**
* Function to fill the selections of the modal
*/
function fillSelect(idSelect, mainWord, quantity)
{
    let i = 2;
    let singular = '';
    let plural = '';
    switch (mainWord) {
        case 'd':
            singular = 'dia'
            plural = 'dias'
            break;
        case 'm':
            singular = 'mes'
            plural = 'meses'
            break;
        case 'y':
            singular = 'año'
            plural = 'años'
            break;
    }
    let html = '<option value="">Selecciona una opción</option>';
    html += '<option value="1"> 1 '+singular+'</option>'
    while (i <= quantity)
    {
        html += '<option value="'+i+'">'+i+' '+plural+'</option>'
        i++
    }
    $(idSelect).html(html)
}

</script>