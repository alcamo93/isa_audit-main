<script>
activeMenu(6, 'Permisos');
function buttonPermission(permit, type, idModule, idSubmodule){
    let color = '';
    let icon = '';
    let tooltip = '';
    if (permit == 1) {
        tooltip += 'Clic para remover permiso';
        color += 'success';
        icon += 'check';
    }else if (permit == 0 || permit === null){
        tooltip += 'Clic para asignar permiso';
        color += 'danger';
        icon += 'times';
    }
    return `<a class="btn btn-${color} btn-link btn-xs" data-toggle="tooltip" title="${tooltip}"
                onclick="setPermission(${permit}, ${idModule}, '${type}', ${idSubmodule})">
                <i class="fa fa-${icon} fa-lg"></i>
            </a>`;
}

/**
 * permissions datatables
 */
const permissionsTable = $('#permissionsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/permissions',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content, 
            data.idProfile = document.querySelector('#filterIdProfile').value
        }
    },
    columns: [
        { data: 'pseud_module', orderable : true },
        { data: 'visualize_c', orderable : false, className: 'td-actions text-center' },
        { data: 'create_c', orderable : false, className: 'td-actions text-center' },
        { data: 'modify_c', orderable : false, className: 'td-actions text-center' },
        { data: 'delete_c', orderable : false, className: 'td-actions text-center' },
        { data: 'has_submodule', orderable : false, className: 'td-actions text-center' }
    ],
    columnDefs: [
        {
            render: ( data, type, row ) => {
                return buttonPermission(data, 'visualize', row.id_module, null);
            },
            targets: 1
        },
        {
            render: ( data, type, row ) => {
                return buttonPermission(data, 'create', row.id_module, null);
            },
            targets: 2
        },
        {
            render: ( data, type, row ) => {
                let button = ''
                if (row.id_module == 8) {
                    button = 'N/A';
                }
                else {
                    button = buttonPermission(data, 'modify', row.id_module, null);
                }
                return button;
            },
            targets: 3
        },
        {
            render: ( data, type, row ) => {
                let button = ''
                if (row.id_module == 8) {
                    button = 'N/A';
                }
                else {
                    button = buttonPermission(data, 'delete', row.id_module, null);
                }
                return button;
            },
            targets: 4 
        },
        {
            render: ( data, type, row ) => {
                if(data == 1){
                    tooltip = 'Clic para ver permisos de submodulos';
                    return `<button class="btn btn-success" onclick="loadSubmodulePermissions(${row.id_module})" 
                                data-toggle="tooltip" title="${tooltip}">
                                Submodulos
                            </button>`;
                }
                else return 'N/A'
            },
            targets: 5
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
 * permissions datatables
 */
const permissionsSubmodulesTable = $('#permissionsSubmodulesTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/permissions/submodule',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content
            data.idProfile = document.querySelector('#filterIdProfile').value
            data.idModule = document.querySelector('#permissionsSubmodulesTable').getAttribute('module')
        }
    },
    columns: [
        { data: 'name_module', orderable : true },
        { data: 'name_submodule', orderable : false, className: 'td-actions text-center' },
        { data: 'visualize_c', orderable : false, className: 'td-actions text-center' },
        { data: 'create_c', orderable : false, className: 'td-actions text-center' },
        { data: 'modify_c', orderable : false, className: 'td-actions text-center' },
        { data: 'delete_c', orderable : false, className: 'td-actions text-center' }
    ],
    columnDefs: [
        {
            render: ( data, type, row ) => {
                return buttonPermission(data, 'visualize', row.id_module, row.id_submodule);
            },
            targets: 2
        },
        {
            render: ( data, type, row ) => {
                return buttonPermission(data, 'create', row.id_module, row.id_submodule);
            },
            targets: 3
        },
        {
            render: ( data, type, row ) => {
                return buttonPermission(data, 'modify', row.id_module, row.id_submodule);
            },
            targets: 4
        },
        {
            render: ( data, type, row ) => {
                return buttonPermission(data, 'delete', row.id_module, row.id_submodule);
            },
            targets: 5
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
 * Reload permissions Datatables
 */
const reloadPermission = () => { permissionsTable.ajax.reload() }
const reloadPermissionKeepPage = () => { permissionsTable.ajax.reload(null, false) }
/**
 * Reload permissions Datatables
 */
const reloadPermissionSubmodules = () => { permissionsSubmodulesTable.ajax.reload() }
const reloadPermissionSubmodulesKeepPage = () => { permissionsSubmodulesTable.ajax.reload(null, false) }
/**
 * Set permission
 */
function setPermission(value, idModule, type, idSubmodule = null) {
    const status = (value == 0 || value === null) ? 1 : 0;
    const idProfile = document.querySelector('#filterIdProfile').value;
    //handler notificaction
    $('.loading').removeClass('d-none')
    $.post('/permissions/set', {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        status: status,
        idModule: idModule, 
        idSubmodule: idSubmodule, 
        idProfile: idProfile,
        type: type
    },
    (data) => {
        $('.loading').addClass('d-none')
        toastAlert(data.msg, data.status);
        if (data.status == 'success') {
            if(idSubmodule) reloadPermissionSubmodules();
            else reloadPermissionKeepPage();
        }
    })
    .fail(e => {
        $('.loading').addClass('d-none')
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Set corporates
 */
function setProfiles(idCorporate, selectorProfiles, callback){
    const selectInput = document.querySelector(selectorProfiles);
    if (idCorporate != '' || idCorporate == 0) {
        // Get all corporates
        $.get(`/profiles/filter/${idCorporate}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        }, 
        (data) => {
            if (data.length > 0) {
                let html = '';
                if ( callback != 'undefined' && typeof callback === 'function' ) {
                    html += `<option value="0">Todos</option>`;
                }
                data.forEach(d => {
                    html += `<option value="${d['id_profile']}">${d['profile_name']} - ${d['type']}</option>`;
                });
                selectInput.innerHTML = html;
                
            }
            else {
                const msg = ( callback != 'undefined' && typeof callback === 'function' ) ? 'Todos' : '';
                const value = ( callback != 'undefined' && typeof callback === 'function' ) ? '' : '0';
                const html = `<option value="${value}">${msg}</option>`;
                selectInput.innerHTML = html;
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
        });
    }
    else selectInput.innerHTML = `<option value=""></option>`;
    if ( callback != 'undefined' && typeof callback === 'function' ) callback();
}

function loadSubmodulePermissions(idModule){
    $('.maintable').addClass('d-none');
    $('.loading').removeClass('d-none');
    setTimeout(() => {
        $('#permissionsSubmodulesTable').attr('module', idModule);
        $('.loading').addClass('d-none');
        $('.subtable').removeClass('d-none');
        reloadPermissionSubmodules()
    }, 1000);
}

function closeSubmodulePermissions (){
    $('.subtable').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        $('#permissionsSubmodulesTable').attr('module', '');
        reloadPermissionSubmodules()
        $('.loading').addClass('d-none')
        $('.maintable').removeClass('d-none');
    }, 1000);
}

</script>