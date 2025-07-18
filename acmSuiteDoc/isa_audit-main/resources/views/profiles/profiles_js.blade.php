<script>
$(document).ready( () => {
    setFormValidation('#setProfileForm');
    setFormValidation('#updateProfileForm');
});
activeMenu(5, 'Perfiles');
/**
 * Profiles datatables
 */
const profilesTable = $('#profilesTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/profiles',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.fIdCustomer = document.querySelector('#filterIdCustomer').value,
            data.fIdCorporate = document.querySelector('#filterIdCorporate').value
        }
    },
    columns: [
        { data: 'profile_name', orderable : true },
        { data: 'corp_tradename', className: 'text-center', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'type', className: 'text-center', orderable : true },
        { data: 'id_profile', className: 'td-actions text-center', orderable : false, visible: ACTIONS }
    ],
    columnDefs: [ 
        {
            render:  ( data, type, row ) => {
                let color = (data == 'Activo') ? 'success' : 'danger';
                return `<span class="badge badge-${color} text-white">${data}</span>`; 
            },
            targets: 2
        },       
        {
            render: ( data, type, row ) => {
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openEditProfile('${row.profile_name}', ${row.id_profile})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteProfile('${row.profile_name}', ${row.id_profile})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
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
 * Reload profiles Datatables
 */
const reloadProfiles = () => { profilesTable.ajax.reload() }
const reloadProfilesKeepPage = () => { profilesTable.ajax.reload(null, false) }
/**
 * Open modal add profile
 */
function openAddProfile(){
    document.querySelector('#setProfileForm').reset();
    $('#setProfileForm').validate().resetForm();
    $('#setProfileForm').find(".error").removeClass("error");
    $('#addModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set profile form 
 */
$('#setProfileForm').submit( (event) => {
    event.preventDefault();
    if($('#setProfileForm').valid()) {
        showLoading('#addModal');
        //handler notificaction
        $.post('{{ asset('/profiles/set') }}', {
            '_token': '{{ csrf_token() }}',
            idCustomer: document.querySelector('#s-idCustomer').value,
            idCorporate: document.querySelector('#s-idCorporate').value,
            profile: document.querySelector('#s-profile').value,
            idStatus: document.querySelector('#s-idStatus').value,
            typeProfile: document.querySelector('#s-typeProfile').value,
        },
        (data) => {
            showLoading('#addModal');
            toastAlert(data.msg, data.status);
            if(data.status == 'success'){
                reloadProfiles();
                $('#addModal').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addModal');
        });
    }
});
/**
 * Open modal edit profile
 */
function openEditProfile(profile, idProfile) {
    document.querySelector('#setProfileForm').reset();
    $('#setProfileForm').validate().resetForm();
    $('#setProfileForm').find(".error").removeClass("error");
    document.querySelector('#titleEdit').innerHTML = `Información: "${profile}"`;
    // get profile
    $.get(`{{asset('/profiles')}}/${idProfile}`,
    {
        '_token':'{{ csrf_token() }}'
    },
    (data) => {
        if (data.length > 0) {
            var profile = data[0];
            document.querySelector('#idProfile').value = profile.id_profile;
            document.querySelector('#u-idCustomer').value = profile.id_customer;
            var idCustomer = document.querySelector('#u-idCustomer').value;
            setCorporates(idCustomer, '#u-idCorporate')
            .then(()=>{
                document.querySelector('#u-idCorporate').value = profile.id_corporate;
                document.querySelector('#u-profile').value = profile.profile_name;
                document.querySelector('#u-idStatus').value = profile.id_status;
                document.querySelector('#u-typeProfile').value = profile.id_profile_type;
            })
            $('#editModal').modal({backdrop:'static', keyboard: false});
        }else{
            reloadProfilesKeepPage();
        }
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Handler to submit update profile form 
 */
$('#updateProfileForm').submit( (event) => {
    event.preventDefault();
    if($('#updateProfileForm').valid()) {
        showLoading('#editModal');
        //handler notificaction
        $.post('{{ asset('/profiles/update') }}', {
            '_token': '{{ csrf_token() }}',
            idProfile: document.querySelector('#idProfile').value,
            idCustomer: document.querySelector('#u-idCustomer').value,
            idCorporate: document.querySelector('#u-idCorporate').value,
            profile: document.querySelector('#u-profile').value,
            idStatus: document.querySelector('#u-idStatus').value,
            typeProfile: document.querySelector('#u-typeProfile').value,
        },
        (data) => {
            showLoading('#editModal');
            toastAlert(data.msg, data.status);
            if(data.status == 'success' || data.status == 'error'){
                reloadProfilesKeepPage();
                $('#editModal').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#editModal');
        });
    }
});
/**
 * Delete profile
 */
function deleteProfile(profile, idElement) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${profile}"?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        $('.loading').removeClass('d-none')
        if (result.value) {
            // send to server
            $.post('{{asset('/profiles/delete')}}',
            {
                '_token':'{{ csrf_token() }}',
                idProfile: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                if(data.status == 'success'){
                    reloadProfilesKeepPage();
                }
                $('.loading').addClass('d-none')
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            })
        }
    });
}
</script>