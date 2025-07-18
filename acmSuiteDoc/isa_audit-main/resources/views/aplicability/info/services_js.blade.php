<script>
/************************ service info ************************/

/**
 * Get contracts matters
 */
function getContractMatters(){
    // set matter in contract
    return new Promise((resolve, reject) => {
        $.get(`/aplicability/resgisters/matters/${currentAR.idAplicabilityRegister}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
        }, 
        data => {
            let html = '<option value="0">Todos</option>';
            if (data.length) {
                data.forEach(e => {
                    html += `<option value="${e.id_contract_matter}">${e.matter}</option>`;
                });
            } else html += `<option disabled value=""}">No se encuentran materias registradas</option>`;
            document.querySelector('#idContractMatter').innerHTML = html;
            resolve(true);
        })
        .fail(e => {
            toastAlert(e.statusText, 'error');
            reject(false);
        });
    });
}
/**
 * Progress Matter
 */
function progressMatter(){
    return new Promise((resolve, reject) => {
        $.get(`/aplicability/resgisters/matter/progress/${currentAR.idAplicabilityRegister}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        },
        data => {
            currentStatus.aplicability = data.aplicability;
            currentStatus.matters = data.matters;
            resolve(true);
        })
        .fail(e=> {
            toastAlert(e.statusText, 'error');
            reject(false);
        });
    });
}
/**
 * Set in audit service
 */
function setInAuditService(){
    return new Promise((resolve, reject) => {
        $.post('/aplicability/audit/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAplicabilityRegister: currentAR.idAplicabilityRegister,
            idAuditProcess: currentAR.idAuditProcess,
            idContract: currentAR.idContract
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}
/************************ service quiz ************************/

/**
 * Get data question by id
 */
function getDataQuestion(idQuestion){
    return new Promise((resolve, reject) => {
        $.get(`/catalogs/questions/${idQuestion}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}
/**
 * Get question by aspects
 */
function getQuestionByAspect(){
    return new Promise ((resolve, reject) => {
        $.get('/aplicability/aspects/questions', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idContractAspect: currentAspect.idContractAspect,
            idAuditProcess: currentAR.idAuditProcess,
            idStatus: currentAspect.idStatus,
            idForm: currentAspect.idForm
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}
function getAnswersAspectService(){
    return new Promise((resolve, reject) => {
        $.get('/aplicability/aspect/answers', { 
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAuditProcess: currentAR.idAuditProcess,
            idContractAspect: currentAspect.idContractAspect,
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
 * get legals by questions
 */
function getDataLegalQuestion(idQuestion){
    return new Promise ((resolve, reject) => {
        $.get(`/aplicability/aspect/question/legal/${idQuestion}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        }); 
    });
}
/**
 * set save comments in aplicability
 */
function setComments(idAplicability, comments){
    return new Promise ((resolve, reject) => {
        $.post('/aplicability/comments/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            comments: comments,
            idAplicability: idAplicability
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        }); 
    });
}
/**
 * Service set answer in aplicability
 */
function setAnswerService(idQuestion, idAnswerQuestion, idAnswerValue, valueCheck){
    return new Promise ((resolve, reject) => {
        $.post('/aplicability/aspect/question/answer', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idContract: currentAR.idContract,
            idAuditProcess: currentAR.idAuditProcess,
            idContractAspect: currentAspect.idContractAspect,
            idAspect: currentAspect.idAspect,
            idContractAspect: currentAspect.idContractAspect,
            idQuestion: idQuestion,
            idAnswerQuestion: idAnswerQuestion,
            idAnswerValue: idAnswerValue,
            setAnswer: valueCheck,
            dependencyInForm: currentAspect.dependencyInForm,
            dependencyFreeInForm: currentAspect.freeDependency,
            questionInForm: currentAspect.questionInForm
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}
/**
 * Service set answer in aplicability
 */
// function setMultiAnswerService(idQuestion, idAnswerQuestion, valueCheck){
//     return new Promise ((resolve, reject) => {
//         $.post('/aplicability/aspect/question/answer/multiple', {
//             _token: document.querySelector('meta[name="csrf-token"]').content,
//             idAnswerQuestion: idAnswerQuestion,
//             idAplicability: document.querySelector('#question-'+idQuestion).getAttribute('id-app'),
//             idContract: currentAR.idContract,
//             idAuditProcess: currentAR.idAuditProcess,
//             idAspect: currentAspect.idAspect,
//             idQuestion: idQuestion,
//             setAnswer: valueCheck
//         },
//         data => {
//             resolve(data);
//         })
//         .fail(e => {
//             reject(e.statusText);
//         });
//     });
// }
/**
 * Set Classify Aspect
 */
function setClassifyAspect(){
    return new Promise ((resolve, reject) => {
        $.post('/aplicability/aspect/classify', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAuditProcess: currentAR.idAuditProcess,
            idContractAspect: currentAspect.idContractAspect,
            idAspect: currentAspect.idAspect
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}
</script>