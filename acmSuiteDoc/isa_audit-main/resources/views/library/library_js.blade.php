<script>
/**
 *
 */
const SOURCE_FILE = {
    NONE : { id : 1, origin : "library" },
    ACTION_PLANT : { id : 2, origin : "action_plan" },
    OBLIGATIONS : { id : 3, origin : "obligations" },
};
const LABEL_FILE = "Elige un documento";
const LABEL_AUDITORY = "Seleccionar plane de accion u obligacione disponibles";
const ID_PROFILE_TYPE = "{{  $idProfileType }}";
const profilesType = {
    ownerGlobal: 1,
    ownerOperative: 2,
    corporate: 3,
    coordinator: 4,
    operative: 5
};

/**
 *
 */
$(document).ready( () => {
    activeMenu(16, 'Biblioteca interna');
    setFormValidation('#formDocument');
    if(ID_PROFILE_TYPE == profilesType.coordinator || ID_PROFILE_TYPE == profilesType.operative){
        setDataAuditory();
    }
});

/**
 * files library tables
 */
const libraryTable = $('#libraryTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: "/assets/lenguage_library_module.json"
    },
    ajax: {
        url: 'library/documents',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.idCustomer = document.querySelector('#filterIdCustomer').value,
            data.idCorporate = document.querySelector('#filterIdCorporate').value,
            data.filterTitle = document.querySelector('#filterName').value,
            data.idCategory = document.querySelector('#filterIdCategory').value,
            data.idSource = document.querySelector('#filterIdSource').value
            data.origin = document.querySelector('#filterOrigin').value
        }
    },
    columnDefs: [
        {
            data: data => {
                return data.category.category;
            },
            className: 'text-center',
            orderable: false,
            targets: 0
        },
        {
            data: data => {
                return data.title;
            },
            className: 'text-center',
            orderable: true,
            targets: 1
        },
        {
            data: data => {
                return formatDate(data.created_at, 'date');
            },
            className: 'text-center',
            orderable: false,
            targets: 2
        },
        {
            data: data => {
                return data.source.source;
            },
            className: 'text-center',
            orderable: false,
            targets: 3
        },
        {
            data: data => {
                return (data.process) ? data.process.audit_processes : 'N/A';
            },
            className: 'text-center',
            orderable: false,
            targets: 4
        },
        {
            render: ( data, type, row ) => {
                let fileName = row.customer.cust_trademark+'-'+row.title;
                const btnDownload = `
                    <button class="btn btn-info btn-link btn-xs download-file" 
                        data-toggle="tooltip" title="Descargar documento"
                        onclick="window.open('/files/download/${row.id_file}')" target="_blank">
                        <i class="fa fa-download fa-lg"></i>
                    </button>`;
                const btnRemove = (row.id_source == 1) ? `
                    <button class="btn btn-danger btn-link btn-xs delete-file" 
                        data-toggle="tooltip" data-file="${row.id_file}" title="Eliminar documento">
                        <i class="fa fa-times fa-lg"></i>
                    </button>` : '';
                return btnDownload+btnRemove;
            },
            className: 'td-actions text-center',
            orderable: false,
            targets: 5,
        },
    ]
});

/**
 *
 */
const reloadProcesses = () => { libraryTable.ajax.reload() } //filtros
const reloadProcessesKeepPage = () => { libraryTable.ajax.reload(null, false) } //actualizar

/**
 *
 */
$("#filterIdCustomer").on('change', function (e) {
    if (ID_PROFILE_TYPE == profilesType.ownerGlobal || ID_PROFILE_TYPE == profilesType.ownerOperative) {
        setCorporatesActive(e.target.value, '#filterIdCorporate', function(){}).then(() => reloadProcesses());
    } else {
        new Promise((resove, reject) => {
            resolve(true);
        });
    }
});

/**
 *
 */
$(".filter-select").on('change', function () {
    reloadProcesses();
});

/**
 *
 */
$(".filter-text").on('keyup', delay(function (e) {
    reloadProcesses();
}, 500));

/**
 * open modal add document
 */
$('#buttonAddDocument').on('click', function(){
    $('#addModal').modal('show');
});

/**
 * open modal add document
 */
$(document).on('change', '.select-customer', function() {
    let auditoryBox = $('#idAuditory');
    auditoryBox.empty();
    auditoryBox.append(new Option(LABEL_AUDITORY, ''));
});

$(document).on('change', '.select-plant', function() {
    setDataAuditory();
});

function setDataAuditory(){
    let idCustomer = document.querySelector('#s-idCustomer').value;
    let idCorporate = document.querySelector('#s-idCorporate').value;

    let auditoryBox = $('#idAuditory');
    $.get(`files/${idCorporate}`, {
        _token: document.querySelector('meta[name="csrf-token"]').content
    }).done(function (data) {
        if(data != null){
            auditoryBox.empty();
            auditoryBox.append(new Option(LABEL_AUDITORY, ''));
            data.forEach(function (row, index) {
                auditoryBox.append(new Option(row.process.audit_processes, row.id_audit_processes));
            });
        }
    }).fail(function (e) {
        auditoryBox.empty();
        auditoryBox.append(new Option(LABEL_AUDITORY, ''));
    });
}

/**
 * load file
 */
window.addEventListener('DOMContentLoaded', function () {
    const inputDocumentFile = document.getElementById('idDocumentFile');
    const labelDocumentFile = document.querySelector('#idDocumentFileLabel');

    inputDocumentFile.addEventListener('change', function (e) {
        let currentFile = e.target.files[0];
        let regex = /(\.zip|\.pdf|\.rar|\.dwg|\.xlsx|\.xls|\.doc|\.docx|\.xlsm|\.pptx|\.txt|\.jpg|\.png|\.mp4|\.html|\.msg|\.jpeg|\.csv)$/i;
        if(currentFile != null){
            let maxSize = 6291456;
            let size = currentFile.size;
            if (!regex.exec(currentFile.name)) {
                inputDocumentFile.value = null;
                labelDocumentFile.innerHTML = LABEL_FILE;
                Swal.fire('El formato permitido es .zip, .pdf, .rar, .dwg, .xlsx, .xls, .doc, .docx, .xlsm, .pptx, .txt, .jpg, .png, .mp4, .html, .msg, .jpeg y .csv', '', 'warning');
            } 
            // Temporal Commented
            // else if(size > maxSize){
            //     inputDocumentFile.value = null;
            //     labelDocumentFile.innerHTML = LABEL_FILE;
            //     Swal.fire('El tamaño maximo del archivo es de 6 Megabytes ', '', 'warning');
            // }
            else {
                labelDocumentFile.innerHTML = currentFile.name;
            }
        } else {
            labelDocumentFile.innerHTML = LABEL_FILE;
        }
    });
});

/**
 * event when selecting link in add modal
 */
$('#idSoruce').on('change', function(e){
    let selectbox = document.getElementById("idAuditory");
    if(SOURCE_FILE.NONE.id != e.target.value){
        selectbox.disabled = false;
    } else {
        selectbox.disabled = true;
        selectbox.value = "";
    }
});

/**
 * event when submit form
 */
$('#formDocument').on('submit', function (e) {
   e.preventDefault();
    if($('#formDocument').valid()) {
        let form = new FormData();
        form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        form.append('idCategory', document.getElementById('idCategory').value);
        form.append('title', document.getElementById('nameFile').value);
        form.append('idFile', null);
        form.append('file', document.getElementById('idDocumentFile').files[0]);
        form.append('idSource', document.getElementById('idSoruce').value);
        form.append('idCustomer', document.getElementById('s-idCustomer').value);
        form.append('idCorporate', document.getElementById('s-idCorporate').value);
        form.append('isLibrary', true);

        switch (parseInt(document.getElementById('idSoruce').value)) {
            case SOURCE_FILE.NONE.id:
                form.append('origin', SOURCE_FILE.NONE.origin);
                break;
            case SOURCE_FILE.ACTION_PLANT.id:
                form.append('idAuditProcesses', document.getElementById('idAuditory').value);
                form.append('origin', SOURCE_FILE.ACTION_PLANT.origin);
                break;
            case SOURCE_FILE.OBLIGATIONS.id:
                form.append('idAuditProcesses', document.getElementById('idAuditory').value);
                form.append('origin', SOURCE_FILE.OBLIGATIONS.origin);
                break;
        }

        showLoading('#addModal');
        $.ajax({
            url: 'files/set',
            data: form,
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false
        }).done(function(data) {
            showLoading('#addModal');
            toastAlert(data.msg, data.status);
            reloadProcessesKeepPage();

            if(data.status === 'success'){
                $('#addModal').modal('hide');
            }
        }).fail(function(e) {
            toastAlert(e.statusText, 'error');
            showLoading('#addModal');
        });
    }
});

/**
 * event when close modal
 */
$('#addModal').on('hidden.bs.modal', function (e) {
    let form = $('#formDocument');
    form.validate().resetForm();
    form.find(".error").removeClass("error");
    document.querySelector('#formDocument').reset();
    document.getElementById("idAuditory").disabled = true;
    document.getElementById('idDocumentFileLabel').innerHTML = LABEL_FILE;
});

$(document).on('click','.delete-file', function(event) {
    let idFile = $(event.currentTarget).data('file');
    Swal.fire({
        title: `¿Estas seguro de eliminar este registro?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $.post('files/delete', {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idFile: idFile
            },(data) => {
                toastAlert(data.msg, data.status);
                if(data.status == 'success'){
                    reloadProcessesKeepPage();
                }
            });
        }
    });
});
</script>
