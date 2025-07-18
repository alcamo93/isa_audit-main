<script>

function setFile(title, idTask, idStatus, idFile) {
    currentTask.idTask = idTask;
    currentTask.idStatus = idStatus;
    currentTask.idFile = idFile;
    cleanForm('#setFile');
    document.querySelector('#modalTitleFile').innerHTML = `Agregar Documento a ${title}`;
    document.querySelector('#idDocumentFileLabel').innerHTML = 'Seleccione el documento a subir';
    $('.loading').removeClass('d-none')
    getDataTaskService()
    .then(data => {
        const {permissions, inUpload, inApproved } = data;
        if (data.file) {
            const {title, url, id_category, category } = data.file;
            // set in form
            document.querySelector('#nameFile').value = title;
            document.querySelector('#idCategory').value = id_category;
            // set in review
            document.querySelector('#titleFile').innerText = title;
            document.querySelector('#categoryFile').innerText = category.category;
            currentTask.urlFile = url;
            currentTask.nameFile = title;
            const btnReplaceFile = document.querySelector('#replaceFile');
            const btnCompleteFile = document.querySelector('#completeFile');
            const btnRejectFile = document.querySelector('#rejectFile');
            if (currentTask.idStatus == 19) {
                btnReplaceFile.disabled = true;
                btnCompleteFile.disabled = true;
                btnRejectFile.disabled = true;
            }
            else {
                btnReplaceFile.disabled = !inUpload;
                btnCompleteFile.disabled = !inApproved;
                btnRejectFile.disabled = !inApproved;
            }
            $('.load-file').addClass('d-none')
            $('.review-file').removeClass('d-none')
        }
        else {
            const inputCategory = document.querySelector('#idCategory');
            const inputNameFile = document.querySelector('#nameFile');
            const inputDocumentFile = document.querySelector('#idDocumentFile');
            inputCategory.disabled = !inUpload;
            inputNameFile.disabled = !inUpload;
            inputDocumentFile.disabled = !inUpload;
            $('.load-file').removeClass('d-none')
            $('.review-file').addClass('d-none')
        }
        $('.loading').addClass('d-none');
        $('#setFileModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none')
        toastAlert(e, 'error');
        console.error(e);
    });
}

$('#setFile').submit( (event) => {
    event.preventDefault() 
    if($('#setFile').valid()) {
        // set data in form
        let form = new FormData();
        form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        form.append('idFile', currentTask.idFile);
        form.append('idCategory', document.getElementById('idCategory').value);
        form.append('title', document.getElementById('nameFile').value);
        form.append('file', document.getElementById('idDocumentFile').files[0]);
        form.append('idSource', 2);
        form.append('idCustomer', currentAR.idCustomer);
        form.append('idCorporate', currentAR.idCorporate);
        form.append('idAuditProcesses', currentAR.idAuditProcess);
        form.append('isLibrary', false);
        form.append('idActionPlan', currentAP.idActionPlan);
        form.append('idTask', currentTask.idTask);
        // send data
        showLoading('#setFileModal');
        setFileService(form)
        .then(data => {
            showLoading('#setFileModal')
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                reloadTasksKeepPage();
                $('#setFileModal').modal('hide');
            }
        })
        .catch(e => {
            showLoading('#setFileModal')
            toastAlert(e, 'error');
            console.error(e);
        });
    }
});

document.querySelector('#idDocumentFile').addEventListener('change', function (e) {
    const currentFile = e.target.files[0];
    // verify extension
    const allowExt = ['zip', 'pdf', 'rar', 'dwg', 'xlsx', 'xls', 'doc', 'docx', 'xlsm', 
        'pptx', 'txt', 'jpg', 'png', 'mp4', 'html', 'msg', 'jpeg', 'csv'];
    const extension = currentFile.name.split('.').slice(-1).pop(); 
    if (!allowExt.includes(extension)) {
        this.value = '';
        toastAlert(`Los formatos permitidos son ${allowExt.join(', .')}`, 'warning');
        return;
    }
    // verify size
    const maxSize = 6291456;
    const mega = 1000000;
    const maxSizeHuman = Math.round(maxSize / mega);
    const size = currentFile.size;
    if(size > maxSize){
        this.value = '';
        toastAlert(`El tamaño maximo del archivo debe ser de ${maxSizeHuman}MB`, 'warning');
        return;
    }
    document.querySelector('#idDocumentFileLabel').innerHTML = currentFile.name;
});

document.querySelector('#downloadFile').addEventListener('click', function (e) {
    window.open(`/files/download/${currentTask.idFile}`);
});

document.querySelector('#replaceFile').addEventListener('click', function (e) {
    $('.load-file').removeClass('d-none')
    $('.review-file').addClass('d-none')
});

document.querySelector('#completeFile').addEventListener('click', function (e) {
    showLoading('#setFileModal');
    completeTaskFileService(true)
    .then(data => {
        showLoading('#setFileModal');
        toastAlert(data.msg, data.status);
        if (data.status == 'success') {
            reloadTasksKeepPage();
            $('#setFileModal').modal('hide');
        }
    })
    .catch(e => {
        showLoading('#setFileModal');
        toastAlert(e, 'error');
        console.error(e);
    })
});

document.querySelector('#rejectFile').addEventListener('click', function (e) {
    showLoading('#setFileModal')
    completeTaskFileService(false)
    .then(data => {
        showLoading('#setFileModal')
        toastAlert(data.msg, data.status);
        if (data.status == 'success') {
            reloadTasksKeepPage();
            $('#setFileModal').modal('hide');
        }
    })
    .catch(e => {
        showLoading('#setFileModal')
        toastAlert(e, 'error');
        console.error(e);
    })
});
</script>