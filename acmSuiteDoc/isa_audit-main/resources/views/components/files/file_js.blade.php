<script>

$(document).ready( () => {
    setFormValidation('#commentsForm');
});

const files = {
    data : null,
    idComment : null,
    idActionPlan: null,
    idObligation: null,
    idTask: null,
    origin: null
}

const filesTable = $('#filesTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/files',
        type: 'POST',
        data:  (data) => {
            data._token = '{{ csrf_token() }}',
            data.idComment = files.idComment,
            data.idActionPlan = files.idActionPlan,
            data.idTask = files.idTask,
            data.idObligation = files.idObligation
        }
    },
    columns: [
        { data: 'title', className: 'td-actions text-center', orderable : true },
        { data: 'id_file', className: 'td-actions text-center', width:150, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                if(files.data){
                    let index = files.data.indexOf(row)
                    if(index == -1 ) files.data.push(row)
                }

                let btnDelete = ''
                let downloadFile = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Descargar" 
                                    href="javascript:downloadFile(${data})">
                                    <i class="fa fa-download fa-lg"></i>
                                </a>`;
                if(USER == row.id_user) btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    href="javascript:deleteFile(${data})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return downloadFile+btnDelete
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
 * Reload select basis Datatables
 */
const reloadFiles = () => {
    files.data = null
    files.data = []
    filesTable.ajax.reload() 
}
const reloadFilesKeepPage = () => {
    files.data = null
    files.data = []
    filesTable.ajax.reload(null, false) 
}
/**
* open comments table
*/
function openFiles(idComment, idActionPlan, idObligation, idTask, origin){
    files.idComment = idComment
    files.idActionPlan = idActionPlan
    files.idObligation = idObligation
    files.idTask = idTask
    files.origin = origin
    files.data = []
    if (origin == 'comments_ap' || origin == 'comments_obligation') $('.comments').addClass('d-none')
    else if (origin == 'action_plan') $('.action').addClass('d-none')
    else if (origin == 'sub_action_plan') $('.subAction').addClass('d-none')
    else if (origin == 'tasks') $('.tasks').addClass('d-none')
    else if (origin == 'obligations') $('.obligations').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        reloadFiles()
        $('.loading').addClass('d-none')
        $('.files').removeClass('d-none')
    }, 1000);
}
/**
* close comments table
*/
function closeFiles(){
    $('.files').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        $('.loading').addClass('d-none')
        if (files.origin == 'comments_ap' || files.origin == 'comments_obligation') $('.comments').removeClass('d-none')
        else if (files.origin == 'action_plan') $('.action').removeClass('d-none')
        else if (files.origin == 'sub_action_plan') $('.subAction').removeClass('d-none')
        else if (files.origin == 'tasks') $('.tasks').removeClass('d-none')
        else if (files.origin == 'obligations') $('.obligations').removeClass('d-none')
        files.idComment = null
        files.idActionPlan = null,
        files.idObligation = null,
        files.idTask = null,
        files.data = null
        files.origin = null
        reloadFiles()
    }, 1000);
}
/**
 * Open modal add form
 */
function openFilesModal (idFile) {
    document.querySelector('#fileForm').reset()
    $('#fileLabel').html('Elige un documento')
    $('#fileModalTitle').html('Adjuntar documento')
    $('#fileForm').validate().resetForm()
    $('#fileForm').find(".error").removeClass("error")
    $('#fileModal').modal({backdrop:'static', keyboard: false})
}
/**
* File selection
*/
function fileSelection(obj)
{
    let uploadFile = obj.files[0]
    let thisInput = $('#'+obj.id)
    let sizeMaxImg = 10000000
    if (uploadFile.type != 'application/pdf')
    {
        Swal.fire('El formato permitido es PDF', '', 'warning')
        thisInput.replaceWith(thisInput.val('').clone(true))
    }
    else if (uploadFile.size > sizeMaxImg) {
        var sizeAlert = sizeMaxImg / 1000;
        Swal.fire(`El peso del documento no puede exceder los ${sizeAlert}MB`, '', 'warning');
        thisInput.replaceWith(thisInput.val('').clone(true));
    }
    else $('#fileLabel').html(obj.files[0].name)
}
/**
 * Download file
 */
function downloadFile (idFile) {
    let index = files.data.findIndex( o => o.id_file === idFile )
    let url = files.data[index].url
    window.open(url, 'Download');
}
/**
* Save modal data
*/
$('#fileForm').submit( (event) => {
    event.preventDefault();
    if($('#fileForm').valid()) {
        // showLoading('#fileModal')
        //handler notificaction
        let title = $('#title-f').val()
        let obj = document.getElementById('file')
        let file = obj.files[0]
        let form = new FormData()
        form.append('_token', '{{ csrf_token() }}')
        form.append('file', file)
        form.append('idComment', files.idComment)
        form.append('idActionPlan', files.idActionPlan)
        form.append('idObligation', files.idObligation)
        form.append('idTask', files.idTask)
        form.append('title', title)
        form.append('origin', files.origin)
        jQuery.ajax({
            url: '/files/set',
            data: form,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success:function(data){
                toastAlert(data.msg, data.status);
                // showLoading('#fileModal')
                reloadFilesKeepPage()
                if(data.status == 'success'){ 
                    $('#fileLabel').html('Elige un documento')
                    $('#fileModal').modal('hide') 
                    if (files.origin == 'action_plan') reloadActionKeepPage()
                    else if (files.origin == 'sub_action_plan') reloadSubActionKeepPage()
                    else if (files.origin == 'tasks') reloadTasksKeepPage()
                    else if (files.origin == 'obligations') reloadObligationsKeepPage();
                }
            }
        })
    }
})
/**
 * Delete comment
 */
function deleteFile(idElement) {
    let index = files.data.findIndex( o => o.id_file === idElement ) 
    Swal.fire({
        title: '¿Estas seguro de eliminar el documento '+files.data[index].title+'?',
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
            $.post('/files/delete',
            {
                '_token':'{{ csrf_token() }}',
                idFile: idElement,
                url: files.data[index].url
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success') {
                    reloadFilesKeepPage();
                    if (files.origin == 'action_plan') reloadActionKeepPage()
                    else if (files.origin == 'sub_action_plan') reloadSubActionKeepPage()
                    else if (files.origin == 'tasks') reloadTasksKeepPage()
                    else if (files.origin == 'obligations') reloadObligationsKeepPage();
                }
            });
        }
    });
}
/**
 * Help files
 */
 function helpAddFiles(){
    var intro = introJs($('#filesModalIntro')[0]);
    intro.setOptions({
        steps: [
            {
                intro: `Utiliza las flechas <br/> Izquiera o Derecha <br/> (Retroceder o Avanzar)`,
            },
            {
                intro: `Con el campo Titulo<br/> podras identificar el documento subido`,
            },
            {
                intro: `Elige un archivo con extensión .pdf<br/> Según el peso de tu archivo y velocidad de intenert sera el tiempo de carga`,
            },
            {
                intro: 'Para guardar la documentación haga clic en el boton Registrar'
            },
        ],
        'tooltipPosition': 'bottom',
        'exitOnOverlayClick': false,
        'showStepNumbers': false,
        'showButtons': false,
        'showBullets': false,
    }).start();
}
</script>