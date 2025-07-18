<script>
/**************** Info *****************/

/**
 * Get matters in audit matters
 */
function getAuditMatters(){
    return new Promise((resolve, reject) => {
        $.get(`/audit/resgisters/matters/${currentAR.idAuditRegister}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        },
        data => {
            let html = '<option value="0">Todos</option>';
            if (data.length) {
                data.forEach(e => {
                    html += `<option value="${e.id_audit_matter}">${e.matter}</option>`;
                });
            } else html += `<option disabled value="">No se encuentran materias registradas</option>`;
            document.querySelector('#idAuditMatter').innerHTML = html;
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
        $.get(`/audit/resgisters/matter/progress/${currentAR.idAuditRegister}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        },
        (data) => {
            currentStatus.audit = data.audit;
            currentStatus.matters = data.matters;
            currentStatus.global = data.total;
            resolve(true);
        })
        .fail(e => {
            toastAlert(e.statusText, 'error');
            reject(false);
        });
    });
}
/**
 * service to server
 */
function setInActionPlanService(){
    return new Promise((resolve, reject) => {
        $.post('/audit/action/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAuditProcess: currentAR.idAuditProcess,
            idAuditRegister: currentAR.idAuditRegister,
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
/**
 * Delete Aspect audit
 */
function deleteAspectService(idAuditMatter, idAuditAspect) {
    return new Promise((resolve, reject) => {
        $.post('/audit/resgisters/aspect/delete', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAuditMatter: idAuditMatter,
            idAuditAspect: idAuditAspect
        },
        data => {
            resolve(data);
        })
        .fail((e)=>{
            reject(e.statusText);
        });
    });
}


/**************** Quiz *****************/

/**
 * evaluate like completed requirement
 */
function setEvaluateRequirement(idRequirement, idSubrequirement, isCompleted){
    return new Promise((resolve, reject) => {
        $.post('/audit/aspect/answer/evaluate', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAuditAspect: currentAspect.idAuditAspect,
            idRequirement: idRequirement,
            idSubrequirement: idSubrequirement,
            complete: isCompleted
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
 *
 */
function getSubrequirementsServices(){
    return new Promise((resolve, reject) => {
        $.get('/audit/quiz/subrequirement', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idContract: currentAR.idContract,
            idAuditAspect: currentAspect.idAuditAspect,
            idAuditProcess: currentAR.idAuditProcess,
            idRequirement: requirementWithSub.idRequirement,
            idApplicationType: currentAspect.idApplicationType
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
 * exec answer service
 */
function execAnswerService(idRequirement, answer, idSubrequirement, recursiveResponse){
    return new Promise((resolve, reject) => {
        $.post('/audit/aspect/requiremnt/answer', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idContract: currentAR.idContract,
            idAuditProcess: currentAR.idAuditProcess,
            idRequirement: idRequirement,
            answer: answer,
            idAspect: currentAspect.idAspect,
            idAuditAspect: currentAspect.idAuditAspect,
            idSubrequirement: idSubrequirement,
            recursiveResponse: (recursiveResponse ? 'true' : 'false')
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
 * Get requirements by aspects
 */
function getRequiremenstByAspect(){
    return new Promise((resolve, reject) => {
        $.get('/audit/quiz/aspect', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAuditProcess: currentAR.idAuditProcess,
            idContract: currentAR.idContract,
            idAspect: currentAspect.idAspect,
            idState: currentAR.idState,
            idCity: currentAR.idCity,
            idApplicationType: currentAspect.idApplicationType,
            idAuditAspect: currentAspect.idAuditAspect
        },
        data => { 
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}

function setClassifyAspectService(){
    return new Promise((resolve, reject) => {
        $.post('/audit/aspect/classify', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAuditAspect: currentAspect.idAuditAspect,
            idAuditProcess: currentAR.idAuditProcess,
            idAspect: currentAspect.idAspect,
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}

/***** Table services ******/

function showBasiesServices(idRequirement){
    return new Promise((resolve, reject) => {
        $.get(`/audit/requirement/basies/${idRequirement}`, {
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

function showFindingService(){
    let idAuditElement = (currentElement.idSubrequirement == null) ? `#link-${currentElement.idRequirement}` : `#linkSub-${currentElement.idSubrequirement}`;
    return new Promise((resolve, reject) => {
        $.get('/audit/aspect/answer', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAudit: document.querySelector(idAuditElement).getAttribute('id-audit'),
            idAuditProcess: currentAR.idAuditProcess,
            idAspect: currentAspect.idAspect,
            idRequirement: currentElement.idRequirement,
            idSubrequirement: currentElement.idSubrequirement
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}

function helpRequirementService(idRequirement){
    return new Promise((resolve, reject) => {
        $.get(`/audit/requirement/${idRequirement}`, {
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

function setFindingService(){
    let idAuditElement = (currentElement.idSubrequirement == null) ? `#link-${currentElement.idRequirement}` : `#linkSub-${currentElement.idSubrequirement}`;
    return new Promise((resolve, reject) => {
        $.post('/audit/requirement/finding/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAuditProcess: currentAR.idAuditProcess,
            idAudit: document.querySelector(idAuditElement).getAttribute('id-audit'),
            idRequirement: currentElement.idRequirement,
            idSubrequirement: currentElement.idSubrequirement,
            idAspect: currentAspect.idAspect,
            finding: document.querySelector('#finding').value,
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}

/******* Sub quiz ********/

/**
 * Evaluate subrequirements for requirements
 */
function evaluateReqComposite(){
    return new Promise((resolve, reject) => {
        $.post('/audit/requirement/evaluate/sub', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idRequirement: requirementWithSub.idRequirement,
            idAuditProcess: currentAR.idAuditProcess,
            idContract: currentAR.idContract,
            idAspect: currentAspect.idAspect,
            idAuditAspect: currentAspect.idAuditAspect,
            evaluateRisk: currentAR.evaluateRisk
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
 *
 */
function helpSubrequirementService(idSubrequirement){
    return new Promise((resolve, reject) => {
        $.get(`/audit/requirement/sub/${idSubrequirement}`, {
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
 * show basies
 */
function showBasiesSubServices(idSubrequirement){
    return new Promise((resolve, reject) => {
        $.get(`/audit/requirement/basies/sub/${idSubrequirement}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        },
        data => {
            resolve(data)
        })
        .fail(e => {
            reject(e.statusText);
        });
    })
} 

/***** Risk services ******/
/**
 * Set answer risk
 */
function setRiskAnswerServiceV2(value, idRequirement, idRiskCategory, idRiskAttribute, idSubrequirement) {
    return new Promise((resolve, reject) => {
        const idAuditElement = (idSubrequirement == null) ? `#link-${idRequirement}` : `#linkSub-${idSubrequirement}`;
        const registerableId = document.querySelector(idAuditElement).getAttribute('id-audit');
        $.ajax({
            type: 'PUT',
            url: `/v2/audit/${registerableId}?option=risk`,
            contentType: 'application/json',
            data: JSON.stringify({
                _token: document.querySelector('meta[name="csrf-token"]').content,
                answer: value,
                registerable_type: 'Audit',
                registerable_id: registerableId,
                id_risk_category: idRiskCategory,
                id_risk_attribute: idRiskAttribute
            }),
        })
        .done(data => {
            resolve(data); 
        })
        .fail(e => {
            reject(e.statusText);
        });
    })
}
/**
 * Set answer risk
 */
function setRiskAnswerService(value, idRequirement, idRiskCategory, idRiskAttribute, idSubrequirement) {
    return new Promise((resolve, reject) => {
        let idAuditElement = (idSubrequirement == null) ? `#link-${idRequirement}` : `#linkSub-${idSubrequirement}`;
        $.post('/audit/aspect/risk/set/answer', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idAudit: document.querySelector(idAuditElement).getAttribute('id-audit'),
            answer: value,
            idRequirement: idRequirement, 
            idSubrequirement: idSubrequirement,
            idRiskCategory: idRiskCategory,
            idRiskAttribute: idRiskAttribute,
            idAuditProcess: currentAR.idAuditProcess,
            idContract: currentAR.idContract,
            evaluateRisk: currentAR.evaluateRisk
        },
        data => {
            resolve(data); 
        })
        .fail(e => {
            reject(e.statusText);
        });
    })
}
/***
 * Get risk Help Audit
 */
function riskHelpAudit(idRiskCategory, idRiskAttribute) {
    return new Promise((resolve, reject) => {
        $.get('/catalogs/risks/help', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idRiskCategory: idRiskCategory,
            idRiskAttribute: idRiskAttribute
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}

function validateSpecificRequirementsService(idAuditProcess, idAspect) {
    return new Promise((resolve, reject) => {
        $.get(`/processes/validate/${idAuditProcess}/specifics/${idAspect}`, {
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
</script>