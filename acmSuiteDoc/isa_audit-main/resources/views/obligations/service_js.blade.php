<script>
/**
 * Get all data by action requirement/subrequirement
 */
function getDataObligationService(){
    return new Promise((resolve, reject) => {
        $.get('/obligations/get', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idObligation: currentAP.idObligation
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText)
        });
    })
}
/************ Files ************/
function setFileService(form){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'files/set',
            data: form,
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false
        }).done(data => {
            resolve(data);
        }).fail(e => {
            reject(e.statusText);
        });
    });
}

function downloadFileService(){
    return new Promise((resolve, reject) => {
        $.get('/files/download', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idFile: currentFile.idFile
        },
        (data) => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    })
}

function completeObligationFileService(status){
    return new Promise((resolve, reject) => {
        $.post('/obligations/complete', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idObligation: currentAP.idObligation,
            status: status
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    })
}
/************ Commnets ************/
/**
 * get all Comments by tasks
 */
function getCommentsServices() {
    return new Promise((resolve, reject) => {
        $.get('/comments', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idObligation: currentAP.idObligation,
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    })
}
/**
 * set comment
 */
function setCommentService() {
    return new Promise((resolve, reject) => {
        $.post('/comments/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idComment: currentComment.idComment,
            idObligation: currentAP.idObligation,
            comment: document.querySelector('#addCommment').value
        },
        (data) => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}
/**
 * delete comment
 */
function deleteCommentService(idComment){
    return new Promise((resolve, reject) => {
        $.post('/comments/delete', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idComment: currentComment.idComment
        },
        (data) => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}
</script>