<script>
/**
 * Open elp section
 */
function openHelp(idRiskCategory, riskCategory){
    $('.loading').removeClass('d-none');
    $('.categories-section').addClass('d-none');
    element.currentIdCategory = idRiskCategory;
    reloadHelp();
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.help-section').removeClass('d-none');
    }, 1000);
}
/**
 * close help section
 */
function closeHelp(){
    $('.loading').removeClass('d-none');
    $('.help-section').addClass('d-none');
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.categories-section').removeClass('d-none');
    }, 1000);
}
/**
 * risk help datatables
 */
const helpTable = $('#helpTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    order: [[ 2, "asc" ]],
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/risks/help',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.idRiskCategory = element.currentIdCategory;
            data.fName = document.querySelector('#filterNameHelp').value,
            data.filterAttribute = document.querySelector('#filterAttribute').value
        }
    },
    columns: [
        { data: 'risk_help', orderable : true },
        { data: 'standard', className: 'text-justify', orderable : true },
        { data: 'value', className: 'text-center', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'id_risk_help', className: 'td-actions text-center', orderable : false, visible: VISIBLE }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                let color = '';
               switch (data) {
                    case 'Activo':
                        color = 'success';
                       break;
                    case 'Inactivo':
                        color = 'danger';
                    break;
                   default:
                       break;
               }
                return `<span class="badge badge-${color} text-white">${data}</span>`; 
            },
            targets: 3
        },
        {
            render: (data, type, row) => {
                let btnEdit = (MODIFY) ? `<button class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    onclick="openEditHelp(${data}, '${row.risk_help}')">
                                    <i class="fa fa-edit fa-lg"></i>
                                </button>` : '';
                let btnDelete = (DELETE) ? `<button class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    onclick="deleteHelp(${data}, '${row.risk_help}')">
                                    <i class="fa fa-times fa-lg"></i>
                                </button>` : '';
                return btnEdit+btnDelete;
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
 * Reload risks/categories Datatables
 */
const reloadHelp = () => { helpTable.ajax.reload(null, false) }
/**
 * open modal new Help
 */
function openHelpModal() {
    // $('#s-standard').summernote('reset');
    document.querySelector('#setHelpForm').reset();
    $('#setHelpForm').validate().resetForm();
    $('#setHelpForm').find(".error").removeClass("error");
    $('#addHelpModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit update risks help form 
 */
$('#setHelpForm').submit( (event) => {
    event.preventDefault()
    let std = document.querySelector('#s-standard').value;
    if (std == '') {
        toastAlert(`El campo 'Criterio' no puede estar vacio`, 'warning');
        return;
    }
    if($('#setHelpForm').valid()) {
        //handler notificaction
        showLoading('#addHelpModal');
        $.post('/catalogs/risks/help/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            riskHelp: document.querySelector('#s-nameHelp').value,
            value: document.querySelector('#s-value').value,
            standard: document.querySelector('#s-standard').value,
            idRiskCategory: element.currentIdCategory,
            idStatus: document.querySelector('#s-idStatusHelp').value,
            idRiskAttribute: document.querySelector('#s-attribute').value,
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadHelp();
            showLoading('#addHelpModal');
            if(data.status == 'success') $('#addHelpModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addHelpModal');
        })
    }
});
/**
 * Edit help
 */
function openEditHelp(idRiskHelp, riskHelp) {
    document.querySelector('#updateHelpForm').reset();
    $('#updateHelpForm').validate().resetForm();
    $('#updateHelpForm').find(".error").removeClass("error");
    getDataHelp(idRiskHelp).then(data => {
        const { id_risk_help, risk_help, value, standard, id_risk_attribute, id_status } = data[0];
        currentHelp.idRiskHelp = id_risk_help;
        document.querySelector('#u-nameHelp').value = risk_help;
        document.querySelector('#u-value').value = value;
        // $('#u-standard').summernote('code', standard);
        document.querySelector('#u-standard').value = standard;
        document.querySelector('#u-attribute').value = id_risk_attribute;
        document.querySelector('#u-idStatusHelp').value = id_status;
        $('#updateHelpModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        toastAlert(e, 'error');
    })
}
/**
 * Handler to submit update risks help form 
 */
$('#updateHelpForm').submit( (event) => {
    event.preventDefault()
    let std = document.querySelector('#u-standard').value;
    if (std == '') {
        toastAlert(`El campo 'Criterio' no puede estar vacio`, 'warning');
        return;
    }
    if($('#updateHelpForm').valid()) {
        //handler notificaction
        showLoading('#updateHelpModal');
        $.post('/catalogs/risks/help/update', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idRiskHelp: currentHelp.idRiskHelp,
            riskHelp: document.querySelector('#u-nameHelp').value,
            value: document.querySelector('#u-value').value,
            standard: document.querySelector('#u-standard').value,
            idStatus: document.querySelector('#u-idStatusHelp').value,
            idRiskAttribute: document.querySelector('#u-attribute').value,
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadHelp();
            showLoading('#updateHelpModal');
            if(data.status == 'success') $('#updateHelpModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#updateHelpModal');
        })
    }
});
/**
 * Delete Help
 */
function deleteHelp(idElement, riskHelp){
    Swal.fire({
        title: `¿Estas seguro de eliminar "${riskHelp}"?`,
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
            $.post('/catalogs/risks/help/delete', {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idRiskHelp: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadHelp();
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            })
        }
    });
}

function getDataHelp(idRiskHelp){
    return new Promise((resolve, reject) => {
        $.get('/catalogs/risks/help/get', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idRiskHelp: idRiskHelp
        },
        (data) => {
            resolve(data);
        })
        .fail((e)=>{
            reject(e.statusText);
        })
    })
}
</script>