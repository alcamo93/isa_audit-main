<script>
$(document).ready( () => {
    setFormValidation('#setIndustryForm');
    setFormValidation('#updateIndustryForm');
});
/*************** Active menu ***************/
activeMenu(8, 'Giros');
/**
 * Industries datatables
 */
const industryTable = $('#industryTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/industries',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.filterName = document.querySelector('#filterName').value
        }
    },
    columns: [
        { data: 'industry', className: 'text-center', orderable : true },
        { data: 'id_industry', className: 'td-actions text-center', orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openEditIndustry(${data}, '${row.industry}')">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteIndustry(${data}, '${row.industry}')">
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
const reloadIndustries = () => { industryTable.ajax.reload() }
const reloadIndustriesKeepPage = () => { industryTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openIndustryModel () {
    document.querySelector('#setIndustryForm').reset();
    $('#setIndustryForm').validate().resetForm();
    $('#setIndustryForm').find(".error").removeClass("error");
    $('#addModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set industries form 
 */
$('#setIndustryForm').submit( (event) => {
    event.preventDefault()
    if($('#setIndustryForm').valid()) {
        showLoading('#addModal')
        //handler notificaction
        $.post('{{ asset('/catalogs/industries/set') }}', {
            '_token': '{{ csrf_token() }}',
            name: document.querySelector('#name').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadIndustriesKeepPage();
            showLoading('#addModal')
            if(data.status == 'success') $('#addModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editModal');
        })
    }
});
/**
 * Open modal edit form
 */
function openEditIndustry (idIndustry, name) {
    document.querySelector('#updateIndustryForm').reset();
    $('#updateIndustryForm').validate().resetForm();
    $('#updateIndustryForm').find(".error").removeClass("error");
    document.querySelector('#titleEdit').innerHTML = `Edición del giro: "${name}"`;
    document.querySelector('#u-name').value = name;
    document.querySelector('#u-name').setAttribute('id_industry', idIndustry);
    $('#editModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit update industries form 
 */
$('#updateIndustryForm').submit( (event) => {
    event.preventDefault()
    if($('#updateIndustryForm').valid()) {
        //handler notificaction
        showLoading('#editModal');
        $.post('/catalogs/industries/update', {
            '_token': '{{ csrf_token() }}',
            id_industry: document.querySelector('#u-name').getAttribute('id_industry'),
            name: document.querySelector('#u-name').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadIndustriesKeepPage();
            showLoading('#editModal');
            if(data.status == 'success') $('#editModal').modal('hide');
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editModal');
        })
    }
});
/**
 * Delete industry
 */
function deleteIndustry(idElement, customerName) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${customerName}"?`,
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
            $.post('/catalogs/industries/delete',
            {
                '_token':'{{ csrf_token() }}',
                id_industry: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadIndustriesKeepPage();
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