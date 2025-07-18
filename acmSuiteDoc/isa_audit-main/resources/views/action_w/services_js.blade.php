<script>
/************** Section Filters *************/
/**
 * get total to counter
 */
function getCounterService(){
    return new Promise((resolve, reject) => {
        $.get('/action/action-plan/counter', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idActionRegister: currentAR.idActionRegister
        },
        (data) => {
            document.querySelector('#count_complete').innerHTML = data.complete;
            document.querySelector('#count_expired').innerHTML = data.expired;
            document.querySelector('#count_progress').innerHTML = data.progress;
            document.querySelector('#count_review').innerHTML = data.review;
            document.querySelector('#count_unassigned').innerHTML = data.unassigned;
            resolve(true);
        })
        .fail(e => {
            reject(e.statusText)
        });
    })
}
/*
 * Set mmaters in select option
 */
function setMatters(){
    return new Promise((resolve, reject) => {
        $.get('/action/matters', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idActionRegister: currentAR.idActionRegister
        },
        (data) => {
            let options = '<option value="0">Todos</option>';
            data.forEach(element => {
                    options += `<option value="${element.id_matter}">${element.matter}</option>`; 
            });
            document.querySelector('#filterIdMatter').innerHTML = options;
            resolve();
        })
        .fail(e => {
            reject(e.statusText)
        });
    })
}
/**
 * Set aspects in select option
 */
function setAspects(idMatter, selectorAspect, callback){
    if (idMatter > 0) {
        $.get('/action/aspects', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idMatter: idMatter
        },
        data => {
            let html = '<option value="0">Todos</option>';
            data.forEach(element => {
                html += `<option value="${element.id_aspect}">${element.aspect}</option>`; 
            });
            document.querySelector(selectorAspect).innerHTML = html;
        })
        .fail(e => {
            toastAlert(e.statusText, 'error');
        });  
    } else {
        let html = `<option value="0">Todos</option>`;
        document.querySelector(selectorAspect).innerHTML = html;
    }
    if ( callback != 'undefined' && typeof callback === 'function' ) {
        callback();
    }
}
/************** Section Action requirements *************/
/**
 * Get all data by action requirement/subrequirement
 */
function getDataActionService(){
    return new Promise((resolve, reject) => {
        $.get('/action/action-plan/data', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idActionPlan: currentAP.idActionPlan
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText)
        });
    })
}
/**
 * Set action in section expired
 */
function setExpiredService(){
    return new Promise((resolve, reject) => {
        $.post('/action/action-expired/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idActionPlan: currentAP.idActionPlan,
            cause: document.querySelector('#s-cause').value,
            realCloseDate: document.querySelector('#s-real_close_date').value
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText)
        });
    })
}
/**
 * Set action in section expired
 */
function setExpiredAgainService(){
    return new Promise((resolve, reject) => {
        $.post('/action/action-expired/set/again', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idActionExpired: currentAP.idActionExpired,
            cause: document.querySelector('#s2-cause').value,
            realCloseDate: document.querySelector('#s2-real_close_date').value
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText)
        });
    })
}
/**
 * Change priority
 */
function setPriority(idActionPlan, value){
    return new Promise((resolve, reject) => {
        $.post('/action/action/priority', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idActionPlan: idActionPlan,
            idPriority: value
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText)
        });
    })
}
/************** Section Tasks *************/
/**
 * Get all data by task 
 */
function getDataTaskService(){
    return new Promise((resolve, reject) => {
        $.get('/action/tasks/data', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idTask: currentTask.idTask
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText)
        });
    })
}
/**
 * Send info set/update task
 */
function sendFormService(action, findUsers){
    return new Promise((resolve, reject) => {
        $.post(action, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idActionPlan: currentTask.idActionPlan,
            idTask: currentTask.idTask,
            title: document.querySelector('#s-title').value,
            task: document.querySelector('#s-task').value,
            initDate: document.querySelector('#s-initDate').value,
            endDate: document.querySelector('#s-endDate').value,
            stage: (currentAP.section == 'action') ? 1 : 2,
            users: findUsers
        },
        (data) => {
            resolve(data)
        })
        .fail(e => {
            reject(e.statusText, 'error');
        });
    })
}
/**
 * Delete Task
 */
function deleteTaskService(){
    return new Promise((resolve, reject) => {
        $.post('/action/tasks/delete', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idTask: currentTask.idTask,
            idActionPlan: currentTask.idActionPlan
        },
        (data) => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
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
            idFile: currentTask.idFile
        },
        (data) => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    })
}

function completeTaskFileService(status){
    return new Promise((resolve, reject) => {
        $.post('/action/tasks/complete', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idTask: currentTask.idTask,
            idActionPlan: currentTask.idActionPlan,
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
            idTask: currentTask.idTask,
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
            idComment: currentTask.idComment,
            idTask: currentTask.idTask,
            comment: document.querySelector('#addCommment').value,
            stage: (currentAP.section == 'action') ? 1 : 2
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
            idComment: currentTask.idComment
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