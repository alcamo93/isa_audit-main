<script>
/*
 * Show risk consequences
 */
function openRiskConsequence(idRiskCategory){
    $('.loading').removeClass('d-none');
    $('.categories-section').addClass('d-none');
    element.idRiskCategory = idRiskCategory;
    reloadConsequences();
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.consequences-section').removeClass('d-none');
    }, 1000);
}
/*
 * close risk consequences
 */
function closeConsequence(){
    $('.loading').removeClass('d-none');
    $('.consequences-section').addClass('d-none');
    element.idRiskCategory = null;
    reloadCategories();
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.categories-section').removeClass('d-none');
    }, 1000);
}
/**
 * risks/consequences datatables
 */
const consequenceTable = $('#consequenceTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/risks/consequences',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.fName = document.querySelector('#filterConsequence').value,
            data.fIdStatus = document.querySelector('#filterIdStatusConsequence').value,
            data.idRiskCategory = element.idRiskCategory
        }
    },
    columns: [
        { data: 'consequence', className: 'text-center', orderable : true },
        { data: 'name_consequence', className: 'text-justify', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'id_risk_consequence', className: 'td-actions text-center', orderable : false }
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
                                    href="javascript:openRiskSpecification(null, null, ${data}, 'consequences')">
                                    <i class="fa fa-list la-lg"></i>
                                </a>`;
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openEditConsequence(${data}, ${row.consequence},'${row.name_consequence}', ${row.id_status})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteConsequence(${data}, '${row.name_consequence}')">
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
 * Reload risks/consequences Datatables
 */
const reloadConsequences = () => { consequenceTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openConsequenceModal() {
    element.idConsequence = null;
    document.querySelector('#setConsequenceForm').reset();
    $('#setConsequenceForm').validate().resetForm();
    $('#setConsequenceForm').find(".error").removeClass("error");
    $('#addConsequenceModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set risks/categories form 
 */
$('#setConsequenceForm').submit( (event) => {
    event.preventDefault()
    if($('#setConsequenceForm').valid()) {
        showLoading('#addConsequenceModal')
        //handler notificaction
        $.post('/catalogs/risks/consequences/set', {
            '_token': '{{ csrf_token() }}',
            numConsequence: document.querySelector('#s-numConsequence').value,
            idStatus: document.querySelector('#s-idStatusC').value,
            nameConsequence: document.querySelector('#s-consequence').value,
            idRiskCategory: element.idRiskCategory
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadConsequences();
            showLoading('#addConsequenceModal')
            if(data.status == 'success') $('#addConsequenceModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addConsequenceModal');
        })
    }
});
/**
 * Open modal edit form
 */
function openEditConsequence(idConsequence, consequence, nameConsequence, idStatus) {
    element.idConsequence = idConsequence;
    document.querySelector('#updateConsequenceForm').reset();
    $('#updateConsequenceForm').validate().resetForm();
    $('#updateConsequenceForm').find(".error").removeClass("error");
    document.querySelector('#u-numConsequence').value = consequence;
    document.querySelector('#u-consequence').value = nameConsequence;
    document.querySelector('#u-idStatusC').value = idStatus;
    $('#editConsequenceModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit update risks/consequences form 
 */
$('#updateConsequenceForm').submit( (event) => {
    event.preventDefault()
    if($('#updateConsequenceForm').valid()) {
        //handler notificaction
        showLoading('#editConsequenceModal');
        $.post('/catalogs/risks/consequences/update', {
            '_token': '{{ csrf_token() }}',
            idConsequence: element.idConsequence,
            numConsequence: document.querySelector('#u-numConsequence').value,
            nameConsequence: document.querySelector('#u-consequence').value,
            idStatus: document.querySelector('#u-idStatusC').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadConsequences();
            showLoading('#editConsequenceModal');
            if(data.status == 'success') $('#editConsequenceModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editConsequenceModal');
        })
    }
});
/**
 * Delete Consequence
 */
function deleteConsequence(idElement, nameConsequence){
    Swal.fire({
        title: `¿Estas seguro de eliminar "${nameConsequence}"?`,
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
            $.post('/catalogs/risks/consequences/delete',
            {
                '_token':'{{ csrf_token() }}',
                idConsequence: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadConsequences();
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