<script>

$(document).ready( () => {
    setFormValidation('#commentsForm');
});

const comments = {
    data: null,
    idObligation: null,
    idActionPlan: null,
}

function commentsTableInstance() {
    return $('#commentsTable').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
        language: {
            url: '/assets/lenguage.json'
        },
        ajax: { 
            url: '/comments',
            type: 'POST',
            data:  (data) => {
                data._token = '{{ csrf_token() }}',
                data.idObligation = comments.idObligation,
                data.idActionPlan = comments.idActionPlan
            }
        },
        columns: [
            { data: 'title', className: 'td-actions text-center', orderable : true },
            { data: 'complete_name', className: 'td-actions text-center', orderable : true },
            { data: 'created_at', className: 'td-actions text-center', width:150, orderable : true },
            { data: 'id_comment', className: 'td-actions text-center', width:150, orderable : false }
        ],
        columnDefs: [
            {
                render: (data, type, row) => {

                    if(comments.data){
                        let index = comments.data.indexOf(row)
                        if(index == -1 ) comments.data.push(row)
                    }

                    let date = data.split(" ")
                    date = date[0].split("-")
                    return `${date[2]}/${date[1]}/${date[0]}`
                },
                targets: 2
            },
            {
                render: (data, type, row) => {

                    let btnEdit = ''
                    let btnDelete = ''
                    let showMoreBtn = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver más" 
                                        href="javascript:showFullComment(${data})">
                                        <i class="fa fa-eye fa-lg"></i>
                                    </a>`;
                    // ORIGIN is a variable set in config_permissions file
                    let files = `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Documentos" 
                                        href="javascript:openFiles(${data}, null, null, null, '${ORIGIN}')">
                                        <i class="fa fa-file-pdf-o fa-lg"></i>
                                    </a>`;
                    // USER is a variable set in config_permissions file
                    if(USER == row.id_user){

                        btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                            href="javascript:openCommentsModal(${data})">
                                            <i class="fa fa-edit fa-lg"></i>
                                        </a>` : '';
                        btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                        href="javascript:deleteComment(${data})">
                                        <i class="fa fa-times fa-lg"></i>
                                    </a>` : '';
                    }
                    return showMoreBtn+files+btnEdit+btnDelete
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
}
/**
 * Reload select basis Datatables
 */
const reloadComments = () => {
    comments.data = null
    comments.data = []
    tables.comments.ajax.reload(null, false) 
}
/**
* open comments table
*/
function openComments(idActionPlan = null, idObligation = null, view){
    comments.idObligation = idObligation
    comments.idActionPlan = idActionPlan
    comments.view = view
    comments.data = []
    if (comments.view == 'view-obligation') { $('.obligations').addClass('d-none') }
    else if (comments.view == 'view-ap') { $('.action').addClass('d-none') }
    else if (comments.view == 'view-sub-ap') { $('.subAction').addClass('d-none') }
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        reloadComments()
        $('.loading').addClass('d-none')
        $('.comments').removeClass('d-none')
    }, 1000);
}
/**
* close comments table
*/
function closeComments(){
    $('.comments').addClass('d-none')
    $('.loading').removeClass('d-none')
    comments.idObligation = null
    comments.idActionPlan = null
    comments.data = null
    setTimeout(() => {
        reloadComments()
        $('.loading').addClass('d-none')
        if (comments.view == 'view-obligation') { $('.obligations').removeClass('d-none') }
        else if (comments.view == 'view-ap') { $('.action').removeClass('d-none') }
        else if (comments.view == 'view-sub-ap') { $('.subAction').removeClass('d-none') }
    }, 1000);
}
/**
* Open Comments modal
*/
/**
 * Open modal add form
 */
function openCommentsModal (idComment = null ) {
    document.querySelector('#commentForm').reset();
    if(idComment){
        let index = comments.data.findIndex( o => o.id_comment === idComment ) 
        $('#commentForm').attr('action', '/comments/update')
        $('#commentModalTitle').html('Editar '+comments.data[index].title)
        $('#btnSubmitComment').html('Editar')
        $('#commentC').attr('idComment', comments.data[index].id_comment)
        $('#commentC').val(comments.data[index].comment)
        $('#titleC').val(comments.data[index].title)
    }
    else{
        $('#commentModalTitle').html('Agregar Comentario')
        $('#btnSubmitComment').html('Registrar') 
        $('#commentForm').attr('action', '/comments/set')
    }
    $('#commentForm').validate().resetForm();
    $('#commentForm').find(".error").removeClass("error");
    $('#commentModal').modal({backdrop:'static', keyboard: false});
}
/**
 * Open show full view modal
 */
function showFullComment (idComment) {
    document.querySelector('#commentForm').reset();
    let index = comments.data.findIndex( o => o.id_comment === idComment )  
    $('#commentFVTitle').html(comments.data[index].title)
    $('#commentFV').html(comments.data[index].comment)
    $('#commentFVModal').modal({backdrop:'static', keyboard: false});
}
/**
* Save modal data
*/
$('#commentForm').submit( (event) => {
    event.preventDefault();
    if($('#commentForm').valid()) {
        showLoading('#commentModal')
        //handler notificaction
        action = document.querySelector('#commentForm').getAttribute('action')
        $.post(action, {
            '_token': '{{ csrf_token() }}',
            idComment : document.querySelector('#commentC').getAttribute('idComment'),
            comment : document.querySelector('#commentC').value,
            title : document.querySelector('#titleC').value,
            idObligation : comments.idObligation,
            idActionPlan : comments.idActionPlan
        },
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#commentModal')
            reloadComments()
            reloadComments()
            if(data.status == 'success') $('#commentModal').modal('hide') 
        });
    }
});
/**
 * Delete comment
 */
function deleteComment(idElement) {
    let index = comments.data.findIndex( o => o.id_comment === idElement ) 
    Swal.fire({
        title: '¿Estas seguro de eliminar el comentario '+comments.data[index].title+'?',
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
            $.post('{{asset('/comments/delete')}}',
            {
                '_token':'{{ csrf_token() }}',
                idComment: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success') reloadComments();
            });
        }
    });
}
</script>