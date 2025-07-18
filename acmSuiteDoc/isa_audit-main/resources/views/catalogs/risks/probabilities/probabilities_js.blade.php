<script>
/*
 * Show risk probabilities
 */
function openRiskProbabilities(idRiskCategory){
    $('.loading').removeClass('d-none');
    $('.categories-section').addClass('d-none');
    element.idRiskCategory = idRiskCategory;
    reloadProbabilities();
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.probabilities-section').removeClass('d-none');
    }, 1000);
}
/*
 * close risk probabilities
 */
function closeProbabilities(){
    $('.loading').removeClass('d-none');
    $('.probabilities-section').addClass('d-none');
    element.idRiskCategory = null;
    reloadCategories();
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.categories-section').removeClass('d-none');
    }, 1000);
}
/**
 * risks/probabilities datatables
 */
const probabilityTable = $('#probabilityTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/risks/probabilities',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.fName = document.querySelector('#filterProbability').value,
            data.fIdStatus = document.querySelector('#filterIdStatusProbability').value,
            data.idRiskCategory = element.idRiskCategory
        }
    },
    columns: [
        { data: 'probability', className: 'text-center', orderable : true },
        { data: 'name_probability', className: 'text-justify', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'id_risk_probability', className: 'td-actions text-center', orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                var color = '';
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
            targets: 2
        },
        {
            render: (data, type, row) => {
                let btnSpecifications = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Mostrar exposición de riesgo" 
                                    href="javascript:openRiskSpecification(${data}, null, null, 'probabilities')">
                                    <i class="fa fa-list la-lg"></i>
                                </a>`;
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openEditProbability(${data}, ${row.probability},'${row.name_probability}', ${row.id_status})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteProbability(${data}, '${row.name_probability}')">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return btnSpecifications+btnEdit+btnDelete;
            },
            targets: 3
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
 * Reload risks/probabilities Datatables
 */
const reloadProbabilities = () => { probabilityTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openProbabilityModal() {
    element.idProbability = null;
    document.querySelector('#setProbabilityForm').reset();
    $('#setProbabilityForm').validate().resetForm();
    $('#setProbabilityForm').find(".error").removeClass("error");
    $('#addProbabilityModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set risks/probabilities form 
 */
$('#setProbabilityForm').submit( (event) => {
    event.preventDefault()
    if($('#setProbabilityForm').valid()) {
        showLoading('#addProbabilityModal')
        //handler notificaction
        $.post('/catalogs/risks/probabilities/set', {
            '_token': '{{ csrf_token() }}',
            numProbability: document.querySelector('#s-numProbability').value,
            idStatus: document.querySelector('#s-idStatusP').value,
            nameProbability: document.querySelector('#s-probability').value,
            idRiskCategory: element.idRiskCategory
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadProbabilities();
            showLoading('#addProbabilityModal')
            if(data.status == 'success') $('#addProbabilityModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addProbabilityModal');
        })
    }
});
/**
 * Open modal edit form
 */
function openEditProbability(idProbability, probability, nameProbability, idStatus) {
    element.idProbability = idProbability;
    document.querySelector('#updateProbabilityForm').reset();
    $('#updateProbabilityForm').validate().resetForm();
    $('#updateProbabilityForm').find(".error").removeClass("error");
    document.querySelector('#titleEditCatgory').innerHTML = `Edición del categoría: "${name}"`;
    document.querySelector('#u-numProbability').value = probability;
    document.querySelector('#u-probability').value = nameProbability;
    document.querySelector('#u-idStatusP').value = idStatus;
    $('#editProbabilityModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit update risks/probabilities form 
 */
$('#updateProbabilityForm').submit( (event) => {
    event.preventDefault()
    if($('#updateProbabilityForm').valid()) {
        //handler notificaction
        showLoading('#editProbabilityModal');
        $.post('/catalogs/risks/probabilities/update', {
            '_token': '{{ csrf_token() }}',
            idProbability: element.idProbability,
            numProbability: document.querySelector('#u-numProbability').value,
            nameProbability: document.querySelector('#u-probability').value,
            idStatus: document.querySelector('#u-idStatusP').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadProbabilities();
            showLoading('#editProbabilityModal');
            if(data.status == 'success') $('#editProbabilityModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editProbabilityModal');
        })
    }
});
/**
 * Delete Probability
 */
function deleteProbability(idElement, nameProbability){
    Swal.fire({
        title: `¿Estas seguro de eliminar "${nameProbability}"?`,
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
            $.post('/catalogs/risks/probabilities/delete',
            {
                '_token':'{{ csrf_token() }}',
                idProbability: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadProbabilities();
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            })
        }
    });
}
</script>