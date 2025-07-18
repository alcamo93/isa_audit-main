<script>
$(document).ready( () => {
    setFormValidation('#setLicenseForm');
    setFormValidation('#updateLicenseForm');
});
activeMenu(3, 'Licenciamineto');
/**
 * licenses datatables
 */
const licensesTable = $('#licensesTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/licenses',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.fLicense = document.querySelector('#filterLicense').value,
            data.fIdStatus = document.querySelector('#filterIdStatus').value
        }
    },
    columns: [
        { data: 'license', orderable : false },
        { data: 'usr_global', className: 'text-center', orderable : false },
        { data: 'usr_corporate', className: 'text-center', orderable : false },
        { data: 'usr_operative', className: 'text-center', orderable : false },
        { data: 'period', className: 'text-center', orderable : false },
        { data: 'status', className: 'text-center', orderable : false, visible: true },
        { data: 'id_license', className: 'td-actions text-center', orderable : false, visible: ACTIONS }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                if (data != 0) {
                    var user = (data != 1) ? 'Usuarios' : 'Usuario';
                    var badge = `<span class="badge badge-secondary">${data} ${user}</span>`;
                }else{
                    var badge = `<span class="badge badge-light">---</span>`;
                }
                return badge;
            },
            targets: 1
        },
        {
            render: (data, type, row) => {
                if (data != 0) {
                    var user = (data != 1) ? 'Usuarios' : 'Usuario';
                    var badge = `<span class="badge badge-secondary">${data} ${user}</span>`;
                }else{
                    var badge = `<span class="badge badge-light">---</span>`;
                }
                return badge;
            },
            targets: 2
        },
        {
            render: (data, type, row) => {
                if (data != 0) {
                    var user = (data != 1) ? 'Usuarios' : 'Usuario';
                    var badge = `<span class="badge badge-secondary">${data} ${user}</span>`;
                }else{
                    var badge = `<span class="badge badge-light">---</span>`;
                }
                return badge;
            },
            targets: 3
        },
        {
            render: (data, type, row) => {
                return `<span class="badge badge-primary">${data}</span>`;
            },
            targets: 4
        },
        {
            render:  ( data, type, row ) => {
                let color = (data == 'Activo') ? 'success' : 'danger';
                return `<span class="badge badge-${color} text-white">${data}</span>`; 
            },
            targets: 5
        },
        {
            render: (data, type, row) => {
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openEditLicense('${row.license}', ${row.id_license})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteLicense('${row.license}', ${row.id_license})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return btnEdit+btnDelete;
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
});
/**
 * Reload licenses Datatables
 */
const reloadLicenses = () => { licensesTable.ajax.reload() }
const reloadLicensesKeepPage = () => { licensesTable.ajax.reload(null, false) }
/**
 * Open modal add form
 */
function openAddLicense () {
    document.querySelector('#setLicenseForm').reset();
    $('#setLicenseForm').validate().resetForm();
    $('#setLicenseForm').find(".error").removeClass("error");
    $('#addModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set license form 
 */
$('#setLicenseForm').submit( (event) => {
    event.preventDefault();
    if($('#setLicenseForm').valid()) {
        if(
            parseInt(document.querySelector('#s-usrGlobals').value) < 1 &&
            parseInt(document.querySelector('#s-usrCorporates').value) < 1 &&
            parseInt(document.querySelector('#s-usrOperatives').value) < 1 
        )   toastAlert("La licencia debe tener al menos un usuario", "error");
        else
        {
            showLoading('#addModal');
            //handler notificaction
            $.post('/licenses/set', {
                '_token': '{{ csrf_token() }}',
                license: document.querySelector('#s-license').value,
                usrGlobals: document.querySelector('#s-usrGlobals').value,
                usrCorporates: document.querySelector('#s-usrCorporates').value,
                usrOperatives: document.querySelector('#s-usrOperatives').value,
                idStatus: document.querySelector('#s-idStatus').value,
                idPeriod: document.querySelector('#s-idPeriod').value,
            },
            (data) => {
                showLoading('#addModal');
                toastAlert(data.msg, data.status);
                if(data.status == 'success'){
                    reloadLicenses();
                    $('#addModal').modal('hide');
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                showLoading('#addModal');
            });
        }
    }
});
/**
 * Open modal edit form
 */
function openEditLicense (license, idLicense) {
    document.querySelector('#updateLicenseForm').reset();
    $('#updateLicenseForm').validate().resetForm();
    $('#updateLicenseForm').find(".error").removeClass("error");
    document.querySelector('#titleEdit').innerHTML = `Información: "${license}"`;
    // get license
    $.get(`/licenses/${idLicense}`,
    {
        '_token':'{{ csrf_token() }}'
    },
    (data) => {
        if (data.length > 0) {
            var license = data[0];
            document.querySelector('#idLicense').value = license.id_license;
            document.querySelector('#u-license').value = license.license;
            document.querySelector('#u-usrGlobals').value = license.usr_global;
            document.querySelector('#u-usrCorporates').value = license.usr_corporate;
            document.querySelector('#u-usrOperatives').value = license.usr_operative;
            document.querySelector('#u-idStatus').value = license.id_status;
            document.querySelector('#u-idPeriod').value = license.id_period;
            $('#editModal').modal({backdrop:'static', keyboard: false});
        }else{
            reloadLicensesKeepPage();
        }
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Handler to submit update license form 
 */
$('#updateLicenseForm').submit( (event) => {
    event.preventDefault();
    if($('#updateLicenseForm').valid()) {
        if(
            parseInt(document.querySelector('#u-usrGlobals').value) < 1 &&
            parseInt(document.querySelector('#u-usrCorporates').value) < 1 &&
            parseInt(document.querySelector('#u-usrOperatives').value) < 1 
        )   toastAlert("La licencia debe tener al menos un usuario", "error");
        else
        {
            showLoading('#editModal');
            //handler notificaction
            $.post('/licenses/update', {
                '_token': '{{ csrf_token() }}',
                idLicense: document.querySelector('#idLicense').value,
                license: document.querySelector('#u-license').value,
                usrGlobals: document.querySelector('#u-usrGlobals').value,
                usrCorporates: document.querySelector('#u-usrCorporates').value,
                usrOperatives: document.querySelector('#u-usrOperatives').value,
                idStatus: document.querySelector('#u-idStatus').value,
                idPeriod: document.querySelector('#u-idPeriod').value,
            },
            (data) => {
                showLoading('#editModal');
                toastAlert(data.msg, data.status);
                if(data.status == 'success' || data.status == 'error'){
                    reloadLicensesKeepPage();
                    $('#editModal').modal('hide');
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                showLoading('#editModal');
            });
        }
    }
});
/**
 * Delete license
 */
function deleteLicense(license, idElement) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${license}"?`,
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
            $.post('/licenses/delete',
            {
                '_token':'{{ csrf_token() }}',
                idLicense: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                if(data.status == 'success'){
                    reloadLicensesKeepPage();
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
            });
        }
    });
}
</script>