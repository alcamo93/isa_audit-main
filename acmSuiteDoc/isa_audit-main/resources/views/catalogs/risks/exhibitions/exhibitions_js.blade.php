<script>
/*
 * Show risk Exhibitions
 */
function openRiskExhibition(idRiskCategory){
    $('.loading').removeClass('d-none');
    $('.categories-section').addClass('d-none');
    element.idRiskCategory = idRiskCategory;
    reloadExhibitions();
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.exhibitions-section').removeClass('d-none');
    }, 1000);
}
/*
 * close risk Exhibitions
 */
function closeExhibition(){
    $('.loading').removeClass('d-none');
    $('.exhibitions-section').addClass('d-none');
    element.idRiskCategory = null;
    reloadCategories();
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.categories-section').removeClass('d-none');
    }, 1000);
}
/**
 * risks/Exhibitions datatables
 */
const exhibitionTable = $('#exhibitionTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/risks/exhibitions',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.fName = document.querySelector('#filterExhibition').value,
            data.fIdStatus = document.querySelector('#filterIdStatusExhibition').value,
            data.idRiskCategory = element.idRiskCategory
        }
    },
    columns: [
        { data: 'exhibition', className: 'text-center', orderable : true },
        { data: 'name_exhibition', className: 'text-justify', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'id_risk_exhibition', className: 'td-actions text-center', orderable : false }
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
                                    href="javascript:openRiskSpecification(null, ${data}, null, 'exhibitions')">
                                    <i class="fa fa-list la-lg"></i>
                                </a>`;
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openEditExhibition(${data}, ${row.exhibition},'${row.name_exhibition}', ${row.id_status})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteExhibition(${data}, '${row.name_exhibition}')">
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
 * Reload risks/Exhibitions Datatables
 */
const reloadExhibitions = () => { exhibitionTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openExhibitionModal() {
    element.idExhibition = null;
    document.querySelector('#setExhibitionForm').reset();
    $('#setExhibitionForm').validate().resetForm();
    $('#setExhibitionForm').find(".error").removeClass("error");
    $('#addExhibitionModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set risks/exhibition form 
 */
$('#setExhibitionForm').submit( (event) => {
    event.preventDefault()
    if($('#setExhibitionForm').valid()) {
        showLoading('#addExhibitionModal')
        //handler notificaction
        $.post('/catalogs/risks/exhibitions/set', {
            '_token': '{{ csrf_token() }}',
            numExhibition: document.querySelector('#s-numExhibition').value,
            idStatus: document.querySelector('#s-idStatusE').value,
            nameExhibition: document.querySelector('#s-exhibition').value,
            idRiskCategory: element.idRiskCategory
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadExhibitions();
            showLoading('#addExhibitionModal')
            if(data.status == 'success') $('#addExhibitionModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addExhibitionModal');
        })
    }
});
/**
 * Open modal edit form
 */
function openEditExhibition(idExhibition, exhibition, nameExhibition, idStatus) {
    element.idExhibition = idExhibition;
    document.querySelector('#updateExhibitionForm').reset();
    $('#updateExhibitionForm').validate().resetForm();
    $('#updateExhibitionForm').find(".error").removeClass("error");
    document.querySelector('#u-numExhibition').value = exhibition;
    document.querySelector('#u-exhibition').value = nameExhibition;
    document.querySelector('#u-idStatusE').value = idStatus;
    $('#editExhibitionModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit update risks/Exhibitions form 
 */
$('#updateExhibitionForm').submit( (event) => {
    event.preventDefault()
    if($('#updateExhibitionForm').valid()) {
        //handler notificaction
        showLoading('#editExhibitionModal');
        $.post('/catalogs/risks/exhibitions/update', {
            '_token': '{{ csrf_token() }}',
            idExhibition: element.idExhibition,
            numExhibition: document.querySelector('#u-numExhibition').value,
            nameExhibition: document.querySelector('#u-exhibition').value,
            idStatus: document.querySelector('#u-idStatusE').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadExhibitions();
            showLoading('#editExhibitionModal');
            if(data.status == 'success') $('#editExhibitionModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editExhibitionModal');
        })
    }
});
/**
 * Delete Exhibition
 */
function deleteExhibition(idElement, nameExhibition){
    Swal.fire({
        title: `¿Estas seguro de eliminar "${nameExhibition}"?`,
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
            $.post('/catalogs/risks/exhibitions/delete',
            {
                '_token':'{{ csrf_token() }}',
                idExhibition: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadExhibitions();
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