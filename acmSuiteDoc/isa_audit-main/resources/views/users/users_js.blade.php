<script>
$(document).ready( () => {
    setFormValidation('#setUserForm');
    setFormValidation('#updateUserForm');
    setFormValidation('#passwordUserForm');
});
activeMenu(7, 'Usuarios');
/**
 * Users datatables
 */
const userTable = $('#usersTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/users',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.fIdCustomer = document.querySelector('#filterIdCustomer').value,
            data.fIdCorporate = document.querySelector('#filterIdCorporate').value
        }
    },
    columns: [
        { data: 'picture', className: 'text-center', orderable : false },
        { data: 'first_name', orderable : true },
        { data: 'last_names', orderable : true },
        { data: 'email', orderable : true },
        { data: 'phone', orderable : true },
        { data: 'profile_name', orderable : true },
        { data: 'status', className: 'text-center', orderable : true },
        { data: 'id_user', className: 'td-actions text-center', orderable : ACTIONS }
    ],
    columnDefs: [
        {
            render: ( data, type, row ) => {
                return `<img id="img_${row.id_user}" src="{{ asset('/assets/img/faces/') }}/${data}" width="50px">`;
            },
            targets: 0
        },
        {
            render:  ( data, type, row ) => {
                return `${row.profile_name} - ${row.type}`; 
            },
            targets: 5
        },
        {
            render:  ( data, type, row ) => {
                var color = (data == 'Activo') ? 'success' : 'danger';
                return `<span class="badge badge-${color} text-white">${data}</span>`; 
            },
            targets: 6
        },
        {
            render: ( data, type, row ) => {
                var btnPassword = (MODIFY) ? `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Cambiar contraseña" 
                                        href="javascript:openModalPassword('${row.complete_name}', ${row.id_user})">
                                        <i class="fa fa-key fa-lg"></i>
                                    </a>` : '';
                var btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    href="javascript:openEditUser('${row.complete_name}', ${row.id_user})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                var btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteUser('${row.complete_name}', ${row.id_user})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return btnPassword+btnEdit+btnDelete;
            },
            targets: 7
        },
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
/**
 * Reload users Datatables
 */
const reloadUsers = () => { userTable.ajax.reload(null, false) }
/**
 * Open modal add user
 */
function openAddUser(){
    document.querySelector('#setUserForm').reset();
    $('#setUserForm').validate().resetForm();
    $('#setUserForm').find(".error").removeClass("error");
    let idCustomer = document.querySelector('#s-idCustomer').value;
    let idCorporate = document.querySelector('#s-idCorporate').value;
    $('#s-idProfile').html('<option value=""></option>')
    if( idCustomer != '' && idCorporate == '' ) setCorporates(idCustomer, '#s-idCorporate');
    if( idCustomer != '' && idCorporate != '' ) setProfilesSelector('#s-idCorporate', '#s-idProfile')
    $('#addModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit set user form 
 */
$('#setUserForm').submit( (event) => {
    event.preventDefault();
    if($('#setUserForm').valid()) {
        showLoading('#addModal');
        // Set user data
        var user = {};
        user['idCustomer'] = document.querySelector('#s-idCustomer').value;
        user['idCorporate'] = document.querySelector('#s-idCorporate').value;
        user['email'] = document.querySelector('#s-email').value;
        user['idProfile'] = document.querySelector('#s-idProfile').value;
        user['idStatus'] = document.querySelector('#s-idStatus').value;
        // Set person data
        var person = {};
        person['name'] = document.querySelector('#s-name').value;
        person['secondName'] = document.querySelector('#s-secondName').value;
        person['lastName'] = document.querySelector('#s-lastName').value;
        //handler notificaction
        $.post('/users/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            user: user,
            person: person
        },
        (data) => {
            showLoading('#addModal');
            toastAlert(data.msg, data.status);
            if(data.status == 'success'){
                reloadUsers();
                $('#addModal').modal('hide');
            }
        })
        .fail((e)=>{
            showLoading('#addModal');
            toastAlert(e.statusText, 'error');
        })
    }
});
/**
 * Open modal edit user
 */
function openEditUser(user, idUser) { 
    document.querySelector('#updateUserForm').reset();
    $('#updateUserForm').validate().resetForm();
    $('#updateUserForm').find(".error").removeClass("error");
    $('#u-idProfile').html('<option value=""></option>')
    document.querySelector('#titleEdit').innerHTML = `Información: "${user}"`;
    $('.loading').removeClass('d-none');
    // get user
    $.get(`/users/${idUser}`, {
        _token: document.querySelector('meta[name="csrf-token"]').content
    },
    (data) => {
        if (data['user'].length > 0) {
            const user = data['user'][0];
            document.querySelector('#idUser').value = user.id_user;
            document.querySelector('#u-idCustomer').value = user.id_customer;
            const idCustomer = document.querySelector('#u-idCustomer').value;
            setCorporates(idCustomer, '#u-idCorporate')
            .then(()=>{
                document.querySelector('#u-idCorporate').value = user.id_corporate;
                document.querySelector('#u-email').value = user.email;
                document.querySelector('#u-idStatus').value = user.id_status;
                // Set person data
                const person = data['person'][0];
                document.querySelector('#idPerson').value = person.id_person;
                document.querySelector('#u-name').value = person.first_name;
                document.querySelector('#u-secondName').value = person.second_name;
                document.querySelector('#u-lastName').value = person.last_name;

                setProfilesSelector('#u-idCorporate', '#u-idProfile')
                .then(() => {
                    document.querySelector('#u-idProfile').value = user.id_profile;
                    $('.loading').addClass('d-none');
                    $('#editModal').modal({backdrop:'static', keyboard: false});
                })
                .catch((e) => {
                    $('.loading').addClass('d-none');
                    toastAlert(e, 'error');
                })                
            })
            
        }else{
            reloadUsers();
        }
    })
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Handler to submit update user form 
 */
$('#updateUserForm').submit( (event) => {
    event.preventDefault();
    if($('#updateUserForm').valid()) {
        showLoading('#editModal');
        // Set user data
        let user = {};
        user['idUser'] = document.querySelector('#idUser').value;
        user['idCustomer'] = document.querySelector('#u-idCustomer').value;
        user['idCorporate'] = document.querySelector('#u-idCorporate').value;
        user['email'] = document.querySelector('#u-email').value;
        user['idProfile'] = document.querySelector('#u-idProfile').value;
        user['idStatus'] = document.querySelector('#u-idStatus').value;
        // Set person data
        let person = {};
        person['idPerson'] = document.querySelector('#idPerson').value;
        person['name'] = document.querySelector('#u-name').value;
        person['secondName'] = document.querySelector('#u-secondName').value;
        person['lastName'] = document.querySelector('#u-lastName').value;
        //handler notificaction
        $.post('/users/update', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            user: user,
            person: person
        },
        (data) => {
            showLoading('#editModal');
            toastAlert(data.msg, data.status);
            if(data.status == 'success'){
                reloadUsers();
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
 * Delete user
 */
function deleteUser(user, idElement) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${user}"?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $('.loading').removeClass('d-none');
            // send to server
            $.post('/users/delete', {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idUser: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                if(data.status == 'success'){
                    reloadUsers();
                }
                $('.loading').addClass('d-none');
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none');
            });
        }
    });
}
/**
 * Open Modal change password
 */
function openModalPassword(user, idUser) {
    document.querySelector('#passwordUserForm').reset();
    $('#passwordUserForm').validate().resetForm();
    $('#passwordUserForm').find(".error").removeClass("error");
    document.querySelector('#idUserPass').value = idUser;
    document.querySelector('#titlePass').innerHTML = `Cambio de contraseña: ${user}`;
    $('#passwordModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Handler to submit update user form 
 */
$('#passwordUserForm').submit( (event) => {
    event.preventDefault();
    if($('#passwordUserForm').valid()) {
        showLoading('#passwordModal');
        //handler notificaction
        $.post('/users/password/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idUser: document.querySelector('#idUserPass').value,  
            newPassword: document.querySelector('#newPassword').value, 
            repitPassword: document.querySelector('#repitPassword').value, 
        },
        (data) => {
            showLoading('#passwordModal');
            toastAlert(data.msg, data.status);
            if(data.status == 'success' || data.status == 'error'){
                reloadUsers();
                $('#passwordModal').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#passwordModal');
        });
    }
});
/**
 * Set the selection options on corporation selection
 */
function setProfilesSelector(idCorporation, selector) {
    return new Promise ((resolve, reject) => {
        let corp  = $(idCorporation).val()
        getProfilesByCorp(corp)
        .then((data) => {
            let html = '<option value=""></option>'
            data.forEach((currentValue, index, array) => {
                html += `<option value="${currentValue.id_profile}">${currentValue.profile_name} - ${currentValue.type}</option>`
            });
            $(selector).html(html)
            resolve(true)
        })
        .catch((e) => {
            reject(e)
        });
    });
}

/**
 * getProfiles by corporate
 */
function getProfilesByCorp(idCorporate) {
    return new Promise((resolve, reject) => {
        $.get(`/profiles/filter/${idCorporate}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        },
        (data) => {
            resolve(data)
        })
        .fail((e)=>{
            reject(e)
        });
    });
}

</script>