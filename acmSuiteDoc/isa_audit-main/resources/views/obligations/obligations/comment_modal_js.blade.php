<script>
function addCommnet(title, idObligation) {
    currentAP.idObligation = idObligation;
    document.querySelector('#commentsTitle').innerHTML =  `<b>Comentarios sobre obligación: ${title}</b>`;
    $('.loading').removeClass('d-none');
    getCommentsServices()
    .then(data => (buildCommentList(data)) )
    .then(data => {
        $('.loading').addClass('d-none');
        $('#commentsModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert(e, 'error');
        console.error(e);
    });
}

function buildCommentList(data) {
    return new Promise((resolve, reject) => {
        $('#commentsBody').find('li').remove();
        let list = '';
        if (data.length > 0) {
            data.forEach(c => {
                const created = formatDate(c.created_at, 'datetime');
                const updated = formatDate(c.updated_at, 'datetime');
                const updatedStr = (created == updated) ? '' : `- Actualizado en ${updated}`;
                const {first_name, second_name, last_name} = c.user.person;
                list += `
                <li class="media border">
                    <div class="media-body p-2">
                        <p class="mt-0 mb-1 font-weight-bold">${first_name} ${second_name} ${last_name}</p>
                        <p class="text-justify mb-1">${c.comment}</p>
                        <p class="font-weight-bold text-muted mb-0">${created} ${updatedStr}</p>
                        <button onclick="deleteComment(${c.id_comment}, '${c.comment}')" data-toggle="tooltip" 
                            title="Borrar este comentario" class="btn btn-danger float-right btn-sm mr-1">
                            <i class="fa fa-trash-o"></i>
                        </button>
                        <button onclick="editComment(${c.id_comment}, '${c.comment}')" data-toggle="tooltip" 
                            title="Modificar este comentario" class="btn btn-warning float-right btn-sm mr-1">
                            <i class="fa fa-pencil"></i>
                        </button>
                    </div>
                </li>`;
            });
        }
        else {
            list += `
                <li class="media border">
                    <div class="media-body p-2 text-center">
                        <h5 class="font-weight-bold text-muted mt-2">Sin comentarios hasta ahora</h5>
                    </div>
                </li>`;
        }
        $('.addCommentInput').addClass('d-none');
        $('.viewComments').removeClass('d-none');
        document.getElementById('commentsBody').insertAdjacentHTML('beforeend', list);
        resolve(true);
    });
}

document.querySelector('#addComment').addEventListener('click', (e) => {
    currentComment.idComment = null;
    cleanForm('#formComment');
    $('.addCommentInput').removeClass('d-none');
    $('.viewComments').addClass('d-none');
});

document.querySelector('#cancelComment').addEventListener('click', (e) => {
    cleanForm('#formComment');
    $('.addCommentInput').addClass('d-none');
    $('.viewComments').removeClass('d-none');
});

$('#formComment').submit((event) => {
    event.preventDefault();
    if($('#formComment').valid()) {
        showLoading('#commentsModal')
        //handler notificaction
        setCommentService()
        .then(data => {
            toastAlert(data.msg, data.status);
            if(data.status == 'success') {
                return getCommentsServices()
            }
        })
        .then(data => (buildCommentList(data)) )
        .then(data => {
            showLoading('#commentsModal')
        })
        .catch(e => {
            showLoading('#commentsModal')
            toastAlert(e, 'error');
            console.error(e);
        });
    }
});

function editComment(idComment, comment){
    cleanForm('#formComment');
    currentComment.idComment = idComment;
    document.querySelector('#addCommment').value = comment;
    $('.addCommentInput').removeClass('d-none');
    $('.viewComments').addClass('d-none');
}

function deleteComment(idComment){
    currentComment.idComment = idComment;
    showLoading('#commentsModal')
    deleteCommentService()
    .then(data => {
        toastAlert(data.msg, data.status);
        if(data.status == 'success') {
            return getCommentsServices()
        }
    })
    .then(data => (buildCommentList(data)) )
    .then(data => {
        showLoading('#commentsModal')
    })
    .catch(e => {
        showLoading('#commentsModal')
        toastAlert(e, 'error');
        console.error(e);
    });
}
</script>