<script>
/*
 * Show specifications
 */
function openRiskSpecification(idProbability, idExhibition, idConsequence, origin){
    current.idProbability = idProbability;
    current.idExhibition = idExhibition;
    current.idConsequence = idConsequence;
    current.origin = origin;
    $('.loading').removeClass('d-none');
    $('.'+origin+'-section').addClass('d-none');
    reloadSpecifications();
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.specifications-section').removeClass('d-none');
    }, 1000);
}
/*
 * close specifications
 */
function closeSpecification(){
    $('.loading').removeClass('d-none');
    $('.specifications-section').addClass('d-none');
    reloadSpecifications();
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.'+current.origin+'-section').removeClass('d-none');
    }, 1000);
}
/**
 * specifications datatables
 */
const specificationTable = $('#specificationTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/risks/specifications',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.fName = document.querySelector('#filterSpecification').value,
            data.fIdStatus = document.querySelector('#filterIdStatusSpecification').value,
            data.idProbability = current.idProbability,
            data.idExhibition = current.idExhibition,
            data.idConsequence = current.idConsequence
        }
    },
    columns: [
        { data: 'specification', className: 'text-justify', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'id_risk_specification', className: 'td-actions text-center', orderable : false }
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
            targets: 1
        },
        {
            render: (data, type, row) => {
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openEditSpecification(${data},'${row.specification}', ${row.id_status})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteSpecification(${data}, '${row.specification}')">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return btnEdit+btnDelete;
            },
            targets: 2
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
 * Reload specifications Datatables
 */
const reloadSpecifications = () => { specificationTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openSpecificationModal() {
    element.idSpecification = null;
    document.querySelector('#setSpecificationForm').reset();
    $('#setSpecificationForm').validate().resetForm();
    $('#setSpecificationForm').find(".error").removeClass("error");
    $('#addSpecificationModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set risks/specifications form 
 */
$('#setSpecificationForm').submit( (event) => {
    event.preventDefault()
    if($('#setSpecificationForm').valid()) {
        showLoading('#addSpecificationModal')
        //handler notificaction
        $.post('/catalogs/risks/specifications/set', {
            '_token': '{{ csrf_token() }}',
            idStatus: document.querySelector('#s-idStatusS').value,
            specification: document.querySelector('#s-specification').value,
            idProbability: current.idProbability,
            idExhibition: current.idExhibition,
            idConsequence: current.idConsequence
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadSpecifications();
            showLoading('#addSpecificationModal')
            if(data.status == 'success') $('#addSpecificationModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addSpecificationModal');
        })
    }
});
/**
 * Open modal edit form
 */
function openEditSpecification(idSpecification, specification, idStatus) {
    document.querySelector('#updateSpecificationForm').reset();
    $('#updateSpecificationForm').validate().resetForm();
    $('#updateSpecificationForm').find(".error").removeClass("error");    
    document.querySelector('#u-specification').value = specification;
    document.querySelector('#u-idStatusS').value = idStatus;
    current.idSpecification = idSpecification;
    $('#editSpecificationModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit update risks/specifications form 
 */
$('#updateSpecificationForm').submit( (event) => {
    event.preventDefault()
    if($('#updateSpecificationForm').valid()) {
        //handler notificaction
        showLoading('#editSpecificationModal');
        $.post('/catalogs/risks/specifications/update', {
            '_token': '{{ csrf_token() }}',
            idSpecification: current.idSpecification,
            specification: document.querySelector('#u-specification').value,
            idStatus: document.querySelector('#u-idStatusS').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadSpecifications();
            showLoading('#editSpecificationModal');
            if(data.status == 'success') $('#editSpecificationModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editSpecificationModal');
        })
    }
});
/**
 * Delete Specification
 */
function deleteSpecification(idElement, specification){
    Swal.fire({
        title: `¿Estas seguro de eliminar "${specification}"?`,
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
            $.post('/catalogs/risks/specifications/delete',
            {
                '_token':'{{ csrf_token() }}',
                idSpecification: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadSpecifications();
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