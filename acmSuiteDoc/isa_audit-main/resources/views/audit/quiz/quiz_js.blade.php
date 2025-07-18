<script>
/*************************** Quiz to Audit ***************************/

/**
 * get requirements 
 */
function getRequirementsAspect(idAspect, matter, aspect, idContract, idAuditAspect, idStatus, idApplicationType){
    // Set Global data
    currentAspect.idAspect = idAspect; 
    currentAspect.matter = matter; 
    currentAspect.aspect = aspect; 
    currentAspect.idStatus = idStatus; 
    currentAspect.idAuditAspect = idAuditAspect;
    currentAspect.idApplicationType = idApplicationType;
    currentAspect.validateWizard = null;
    // Show screen wizard
    initLoadQuiz();
    $(".auditMatterTitle").html(currentAspect.matter);
    $(".auditAspectTitle").html(currentAspect.aspect);
    removeQuiz();
    // Get requirements by aspects
    getRequiremenstByAspect() 
    .then(data => {
        if (data.status == 'success') {
            currentAspect.totalRequirements = data.requirements.length;
            drawFormWizard()
            .then(res => drawQuiz(data.requirements, data.risk) )
            .then(res => initWizardBS() )
            .then(res => getAnswersAspect() )
            .then(res => {
                $('.loading').addClass('d-none');
                $('.audit-card').removeClass('d-none');
            })
            .catch(e => {
                cancelLoadQuiz();
                toastAlert('Algo salio mal al cargar la evaluación', 'error');
            })
        }else{
            cancelLoadQuiz();
            okAlert(data.title, data.msg, data.status);
        }
    })
    .catch(e => {
        console.log(e)
        cancelLoadQuiz();
        toastAlert(e, 'error');
    });
}
/**
 * hide main class
 */
function initLoadQuiz(){
    $('.loading').removeClass('d-none');
    $('.matters-list-card').addClass('d-none');
}
/**
 * show main class
 */
function cancelLoadQuiz(){
    $('.loading').addClass('d-none');
    $('.matters-list-card').removeClass('d-none');
}
/**
 * close quiz
 */
function closeAudit(){
    reloadaspectsRegister();
    removeQuiz();
    $('.loading').removeClass('d-none');
    $('.audit-card').addClass('d-none');
    setTimeout( () => {
        $('.loading').addClass('d-none');
        $('.matters-list-card').removeClass('d-none');
    }, 1000);
}
/**
 * Control wizard
 */
function controlWizard(action, move = 0){
    if (move == 0) {
        $('#form-wizard').bootstrapWizard(action);
        document.querySelector("#prevButton").disabled = true;
        document.querySelector("#nextButton").disabled = true;
        setTimeout(() => {
            document.querySelector("#prevButton").disabled = false;
            document.querySelector("#nextButton").disabled = false;
        }, 500);
    }else{
        $('#form-wizard').bootstrapWizard(action, move);
    } 
}
/**
 * question delete risk advance to answer update
 */
function setAnswer(idRequirement, answer, idSubrequirement, obviateResponse = false) { 
    let restrict = null;
    let currentValue = null;
    if (idSubrequirement == null) {
        restrict = document.querySelector('#link-'+idRequirement).getAttribute('evaluate-risk');
        currentValue = $('input[name=audit'+idRequirement+']:checked').val();
    }
    else {
        restrict = document.querySelector('#linkSub-'+idSubrequirement).getAttribute('evaluate-risk');
        currentValue = $('input[name=subAudit'+idSubrequirement+']:checked').val();
    }
    if (restrict == 'yes' && currentAR.evaluateRisk) {
        if (currentValue != 0) {
            Swal.fire({
                title: `¿Estás seguro de cambiar la respuesta?`,
                text: 'El cambio eliminará la evaluación de riesgo actual',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, cambiar mi respuesta!',
                cancelButtonText: 'No, cancelar!'
            }).then((result) => {
                if (result.value) {
                    questionTypeAnswer(idRequirement, answer, idSubrequirement);
                }
            });
        }
    }
    else {
        questionTypeAnswer(idRequirement, answer, idSubrequirement);
    }
}
/**
 * question no apply 
 */
function questionTypeAnswer(idRequirement, answer, idSubrequirement) {
    if (idSubrequirement != null && answer == 2) {
        Swal.fire({
            title: `¿Deseas seleccionar todos los subrequerimientos como No aplica?`,
            text: 'Todos los subrequerimientos se marcaran como No aplica',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, marcar como No aplica!',
            cancelButtonText: 'No, cancelar!'
        }).then((result) => {
            // pasar el resultado de la promesa de los botones
            execAnswer(idRequirement, answer, idSubrequirement, result.value);
        });
    }
    else {
        execAnswer(idRequirement, answer, idSubrequirement, false);
    }
}
/**
 * Set audit answer in each step
 */
function execAnswer(idRequirement, answer, idSubrequirement, obviateResponse) {
    execAnswerService(idRequirement, answer, idSubrequirement, obviateResponse)
    .then(data => {
        toastAlert(data.msg, data.status);
        if (data.status == 'success') {
            // is requirement or subrequirement
            if (idSubrequirement == null) {
                let step = document.querySelector('#link-'+idRequirement);
                // set properties
                step.setAttribute('id-audit', data.idAudit);
                if (answer == 0) {
                    const isParent = step.getAttribute('isParent');
                    const setFinding = (isParent == 'true') ? 'null' : 'true';
                    step.setAttribute('finding', setFinding);
                    if (currentAR.evaluateRisk) {
                        step.setAttribute('evaluate-risk', 'yes');
                        $('#sectionRisk-'+idRequirement).removeClass('d-none');
                        okAlert(
                            'Nivel De Riesgo', 
                            'Al no cumplir este requerimiento es necesario evaluar el Nivel de Riego', 
                            'info'
                        );
                    }
                }
                else {
                    $('#sectionRisk-'+idRequirement).addClass('d-none');
                    $('h6[name=interpretationRisks-'+idRequirement+']').addClass('d-none');
                    $('select[name=inputSelectRisks-'+idRequirement+']').val('');
                    step.setAttribute('evaluate-risk', 'no');
                    step.setAttribute('risk-complete', 'no');
                    step.setAttribute('finding', 'null');
                }
                evaluateOnlyTab(step);
            }
            else {
                let step = document.querySelector('#linkSub-'+idSubrequirement);
                step.setAttribute('id-audit', data.idAudit);
                if (answer == 0) {
                    $('#sectionSubRisk-'+idSubrequirement).removeClass('d-none');
                    const evaluate = (currentAR.evaluateRisk) ? 'yes' : 'no';
                    step.setAttribute('evaluate-risk', evaluate);
                    step.setAttribute('finding', 'true');
                }
                else {
                    $('#sectionSubRisk-'+idSubrequirement).addClass('d-none');
                    $('h6[name=interpretationSubRisks-'+idSubrequirement+']').addClass('d-none');
                    $('select[name=inputSelectSubRisks-'+idSubrequirement+']').val('');
                    step.setAttribute('evaluate-risk', 'no');
                    step.setAttribute('risk-complete', 'no');
                    step.setAttribute('finding', 'null');
                }
                if (obviateResponse) {
                    getSubrequirementsAnswers()
                    .then(data => {
                        console.log(data);
                    })
                    .catch(e => {
                        console.log(e);
                    });
                }
                evaluateSubWizard(step)
                evaluateOnlyTab(step, false);
            }
        }
    })
    .catch(e => {
        toastAlert(e, 'error');
    });
}
// function checkTabParent(step){
//     let childComplete = step.getAttribute('childs-complete');
//     console.log(childComplete);
// }
/**
 * Draw form wizard
 */
function drawFormWizard(){
    return new Promise((resolve, reject) => {
        let html = '';
        html += `<div id="form-wizard" class="card-body">
                    <ul class="wizard-list" id="quiz-list"></ul>
                    <div id="btnSteps" class="card-footer text-center">
                        <button type="button" id="prevButton" class="btn btn-success btn-wd btn-back pull-left" onclick="controlWizard('previous')">Anterior</button>
                        <button type="button" id="nextButton" class="btn btn-success btn-wd btn-next pull-right" onclick="controlWizard('next')">Siguiente</button>`;
            if (currentAspect.idStatus != AUDITED) {
                html += `<button type="button" class="btn btn-success btn-wd btn-finish pull-right" onclick="onFinishWizard()">Finalizar</button>`;
            }           
                html += `<div class="clearfix"></div>
                    </div>`;
        html += `<div class="tab-content" id="quiz-list-tab"></div>
            </div>`;
        document.getElementById('wizardForm').insertAdjacentHTML('beforeend', html); 
        resolve(true);
    });
}
/**
 * Draw quiz
 */
function drawQuiz(dataRequirements, dataRisk){
    return new Promise((resolve, reject) => {
        dataRequirements.forEach( (req, list) => {
            const r = req.requirement;
            let quizlist = `<li class="wizard-element">
                            <a class="nav-link ${(list != 0) ? '' : 'active'}" href="#tab${r.id_requirement}" 
                            id="link-${r.id_requirement}" 
                            evaluate-risk="no"
                            risk-complete="no"
                            finding="null"
                            id-audit="null"
                            isParent="${ (r.has_subrequirement == 1) ? 'true' : 'false' }"`;
                            if (r.has_subrequirement == 1) {
                                quizlist += `childs-complete="${(req.complete == 1) ? 'true' : 'false'}"`;
                            }
                            else {
                                quizlist += `childs-complete="true"`;
                            }
                            quizlist += `
                            data-toggle="tab" role="tab" aria-controls="tab${r.id_requirement}" 
                            aria-selected="true">${(list+1)}</a>
                        </li>`;
            document.getElementById('quiz-list').insertAdjacentHTML('beforeend', quizlist);
            let quizTabs = `<div class="tab-pane fade show ${(list != 0) ? '' : 'active'}" id="tab${r.id_requirement}" role="tabpanel">
                        <div class="row d-flex justify-content-center">
                            <div class="col-sm-10 col-md-9">
                                <p id="link-${r.id_requirement}-panswer" class="d-none text-center text-danger">
                                    ${(r.has_subrequirement == 1) 
                                        ? 'Para establecer una respuesta clic en el botón Subrequerimientos (<i class="fa fa-chevron-right fa-lg"></i>)' 
                                        : 'Aún no se ha elegido una respuesta'}
                                </p>
                                <p id="link-${r.id_requirement}-psub" class="d-none text-center text-danger">
                                    <span>Hubo cambios en los subrequerimientos (<i class="fa fa-chevron-right fa-lg"></i>)</span><br>
                                    <span>Por favor vuleve a evaluar, clic en botón Finalizar (<i class="fa fa-check-circle fa-lg"></i>)</span>
                                </p>
                                <p id="link-${r.id_requirement}-child" class="d-none text-center text-danger">Los subrequerimientos estan incompletos (<i class="fa fa-chevron-right fa-lg"></i>)</p>
                                <p id="link-${r.id_requirement}-prisk" class="d-none text-center text-danger">Esta pendiente evaluar la sección de Nivel de Riesgo</p>
                                <p id="link-${r.id_requirement}-pfinding" class="d-none text-center text-danger">
                                    Es necesario agregar un Hallazgo en este requerimiento ( <i class="fa fa-pencil"></i> )
                                </p>
                            </div>
                        </div>
                        <p class="font-weight-bold text-center">
                            Requerimiento No. ${r.no_requirement}<br>
                            ${r.requirement}
                        </p>
                        <div class="row">
                            <div class="col">
                                <div class="table-responsive"> 
                                    <table 
                                        id="table-link-${r.id_requirement}" 
                                        class="table table-striped table-hover" 
                                        cellspacing="0" width="100%"
                                    >
                                        <thead>
                                            <tr>
                                                <th>Descripción</th>
                                                <th class="text-center">Nivel de Riesgo</th>
                                                <th class="text-center">Tipo de Aplicabilidad</th>
                                                <th class="text-center">Información</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th class="text-justify">${ (r.description === null) ? '' : r.description }</th>
                                                <th class="text-center">${ (r.condition === null) ? '' : r.condition.condition }</th>
                                                <th class="text-center">${ (r.application_type === null) ? '' : r.application_type.application_type}</th>
                                                <th class="text-center td-actions">
                                                    <a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Fundamentos legales" 
                                                        onclick="showBasies(${r.id_requirement}, 'No. ${r.no_requirement}')">
                                                        <i class="fa fa-list-ol fa-lg"></i>
                                                    </a>
                                                    <a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Criterio de aceptación" 
                                                        onclick="aceptanceRequirement(${r.id_requirement}, 'No. ${r.no_requirement}')">
                                                        <i class="fa fa-check-square fa-lg"></i>
                                                    </a>
                                                    <a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Registrar Hallazgo" 
                                                        onclick="showFinding(${r.id_requirement}, 'No. ${r.no_requirement}', null, ${r.has_subrequirement})">
                                                        <i class="fa fa-pencil fa-lg"></i>
                                                    </a>
                                                    <a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ayuda para el auditor" 
                                                        onclick="helpRequirement(${r.id_requirement}, 'No. ${r.no_requirement}')">
                                                        <i class="fa fa-question-circle fa-lg"></i>
                                                    </a>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>`;
                    if (r.has_subrequirement == 1) {
                        quizTabs += `<div class="row">
                            <div class="col text-center"> 
                                <button type="button" class="btn btn-primary data-toggle="tooltip"
                                    title="Subrequerimientos" onclick="getSubrequirements(${r.id_requirement}, '${r.no_requirement}', ${currentAR.idContract}, ${currentAspect.idAspect}, ${currentAspect.idStatus}, ${currentAspect.idAuditAspect}, ${currentAspect.idApplicationType});">
                                    Subrequerimientos <i class="fa fa-chevron-right fa-lg"></i>
                                </button>
                            </div>
                        </div>`;   
                    }
                quizTabs += `<fieldset>
                            <div class="form-group">
                                <div class="row">
                                    <label class="col-12 text-center control-label">Respuesta</label>
                                    <div class="col-12 text-center">
                                        <div class="form-check form-check-radio my-radio">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" ${ (currentAspect.idStatus == FINISHED || r.has_subrequirement == 1) ? 'disabled' : '' }
                                                    data-rule-required="true" data-msg-required="Seleccione una de la respuestas disponibles" 
                                                    name="audit${r.id_requirement}" value="0"
                                                    onclick="setAnswer(${r.id_requirement}, this.value, null)">
                                                <span class="form-check-sign"></span>
                                                No Cumple
                                            </label>
                                        </div>
                                        <div class="form-check form-check-radio my-radio">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" ${ (currentAspect.idStatus == FINISHED || r.has_subrequirement == 1) ? 'disabled' : '' }
                                                    name="audit${r.id_requirement}" value="1"
                                                    onclick="setAnswer(${r.id_requirement}, this.value, null)">
                                                <span class="form-check-sign"></span>
                                                Cumple
                                            </label>
                                        </div>
                                        <div class="form-check form-check-radio my-radio">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" ${ (currentAspect.idStatus == FINISHED || r.has_subrequirement == 1) ? 'disabled' : '' }
                                                    name="audit${r.id_requirement}" value="2"
                                                    onclick="setAnswer(${r.id_requirement}, this.value, null)">
                                                <span class="form-check-sign"></span>
                                                No Aplica
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>`;
            if (r.has_subrequirement != 1 && currentAR.evaluateRisk) {
                quizTabs += `<div class="row d-none" id="sectionRisk-${r.id_requirement}">`;
                        dataRisk.forEach((cat, i) => {
                            quizTabs += `
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <h5 class="title text-center">${cat.risk_category}</h5>
                                <h6 
                                    class="title text-center badge-success d-none"
                                    name="interpretationRisks-${r.id_requirement}"
                                    id="valueRisk-${r.id_requirement}-${cat.id_risk_category}">
                                </h6>`;
                    cat.attributes.forEach(att => {
                    
                    quizTabs += `<div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-3 style="padding-bottom: 0;">
                                                    <label>
                                                        ${att.risk_attribute}
                                                    </label>
                                                </div>
                                                <div class="pl-0 pr-0 col-9 justify-content-start style="padding-bottom: 0;"">
                                                    <a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Valoración para ${att.risk_attribute}" 
                                                        onclick="openHelp(${cat.id_risk_category}, ${att.id_risk_attribute}, '${att.risk_attribute}')">
                                                        <i class="fa fa-question-circle fa-lg"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <select
                                                ${ (currentAspect.idStatus == FINISHED) ? 'disabled' : '' }
                                                id="risk-r${r.id_requirement}-c${cat.id_risk_category}-a${att.id_risk_attribute}"
                                                name="inputSelectRisks-${r.id_requirement}"
                                                class="form-control"
                                                data-rule-required="true"
                                                data-msg-required="Selecciona una opción"
                                                onchange="setRiskAnswer(this.value, ${r.id_requirement}, ${cat.id_risk_category}, ${att.id_risk_attribute}, null)"
                                            >
                                                <option value=""></option>`;
                                att.valuations.forEach(val => {
                                    quizTabs += `<option value="${val.value}">${val.risk_help}</option>`
                                });   
                                quizTabs += `</select>
                                        </div>
                                    </div>
                                </div>`;
                            });
                quizTabs += `</div>`;
                        });
                quizTabs += `</div>`;
            }
            document.getElementById('quiz-list-tab').insertAdjacentHTML('beforeend', quizTabs);
        });
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        });
        resolve(true);
    });
}
/**
 * Get answers save previus
 */
function getAnswersAspect(){
    return new Promise ((resolve, reject) => {
        // if (currentAspect.idStatus == AUDITED || currentAspect.idStatus == AUDITING) {
            $.get('/audit/aspect/answers', {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idAuditAspect: currentAspect.idAuditAspect,
            },
            data => {
                // Set answers in cuestions 
                data.forEach( (req, idx) => {
                    const isParent = (req.requirement.has_subrequirement == 1) ? true : false;
                    const findingAttr = (req.finding != null) ? true : false;
                    const isNegative = (req.answer == 0) ? true : false;
                    const conditionNegative = !( (findingAttr && isNegative) || isParent );
                    const requireFinding = (isNegative) ? conditionNegative : false;
                    const step = document.querySelector('#link-'+req.id_requirement);
                    // Values answers
                    $('input[name=audit'+req.id_requirement+'][value='+req.answer+']').prop('checked', true); 
                    step.setAttribute('id-audit', req.id_audit);
                    step.setAttribute('finding', requireFinding);
                    if (isNegative && currentAR.evaluateRisk) {
                        step.setAttribute('evaluate-risk', 'yes');
                        if (isParent) {
                            step.setAttribute('risk-complete', 'yes');
                        }
                        else if (req.risk_answers.length == currentAR.totalRisk) {
                            step.setAttribute('risk-complete', 'yes');
                        }
                        // show section risk level
                        $('#sectionRisk-'+req.id_requirement).removeClass('d-none');
                        req.risk_answers.forEach(ris => {
                            const id = `risk-r${req.id_requirement}-c${ris.id_risk_category}-a${ris.id_risk_attribute}`;
                            document.getElementById(id).value = ris.answer;
                        });
                        // set total 
                        req.risk_totals.forEach(j => {
                            $('#valueRisk-'+req.id_requirement+'-'+j.id_risk_category).removeClass('d-none')
                            document.querySelector('#valueRisk-'+req.id_requirement+'-'+j.id_risk_category).innerHTML = j.interpretation;
                        });
                    }
                    // evaluate per step wizard with response
                    evaluateOnlyTab(step, false);
                });
                // Show advance in contract aspect
                currentAspect.totalAnswers = document.querySelector('#quiz-list').getElementsByClassName('complete').length;
                if (currentAspect.totalRequirements == currentAspect.totalAnswers){
                    if (currentAspect.idStatus == AUDITED) {
                        okAlert('Editar auditoria', 'Se guardaran las respuestas sin necesidad de dar clic en finalizar', 'info');
                    }else{
                        toastAlert('Ya puedes finalizar la evaluación', 'info', 5000);
                        // controlWizard('next', currentAspect.totalAnswers-1);
                    }
                }
                // show pending alerts
                resolve(true);
            })
            .fail(e => {
                toastAlert(e.statusText, 'error');
                reject(false);
            });
        // }
        // else {
        //     resolve(true)
        // }
    });
}
/*
 * show finding
 */
function showFinding(idRequirement, noRequirement, idSubrequirement, hasSubrequirement){
    $('.loading').removeClass('d-none');
    if (hasSubrequirement == 1) {
        toastAlert(`Registra los hallazgos en los Subrequerimientos`, 'info');
        $('.loading').addClass('d-none');
        return;
    }
    document.querySelector('#formFinding').reset();
    $('#formFinding').validate().resetForm();
    $('#formFinding').find(".error").removeClass("error");
    currentElement.idRequirement = idRequirement;
    currentElement.idSubrequirement = idSubrequirement;
    document.querySelector('#showFindingTitle').innerHTML = `Hallazgo de: ${noRequirement}`;
    showFindingService()
    .then(data => {
        if (data.auditAnswer.length > 0){
            if (data.auditAnswer[0].finding == null) {
                if (data.recomendations.length > 0) {
                    document.querySelector('#finding').value = data.recomendations[0].recomendation;
                }
            }
            else document.querySelector('#finding').value = data.auditAnswer[0].finding;
            $('#showFindingModal').modal({backdrop:'static', keyboard: false}); 
        }
        else toastAlert('Es necesario primero elegir una respuesta', 'info');
        $('.loading').addClass('d-none');
    })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert('Algo salio mal al abrir el Hallazgo', 'info');
    })
}
/*
 * Set finding
 */
$('#formFinding').submit( (event) => {
    event.preventDefault();
    if($('#formFinding').valid() && currentAspect.idStatus != FINISHED) {
        showLoading('#showFindingModal');
        //handler notificaction
        setFindingService()
        .then(data => {
            showLoading('#showFindingModal');
            toastAlert(data.msg, data.status);
            if(data.status == 'success'){
                $('#showFindingModal').modal('hide');
                if (currentElement.idSubrequirement == null) {
                    const step = document.querySelector('#link-'+currentElement.idRequirement);
                    step.setAttribute('finding', 'false');
                    evaluateOnlyTab(step);
                }
                else {
                    const step = document.querySelector('#linkSub-'+currentElement.idSubrequirement);
                    step.setAttribute('finding', 'false');
                    evaluateSubWizard(step);
                }
            }
        })
        .catch(e => {
            showLoading('#showFindingModal');
            toastAlert('Algo salio mal al registrar el Hallazgo', 'error');
        })
    }
});
/**
 * Help to audit
 */
function helpRequirement(idRequirement, noRequirement){
    $('.loading').removeClass('d-none');
    helpRequirementService(idRequirement)
    .then(data => {
        if(data[0].help_requirement === null){
            $('.loading').addClass('d-none');
            toastAlert('No cuenta con ayuda', 'warning');
        }else{
            document.querySelector('#helpRequirementTitle').innerHTML = `Ayuda para requerimiento: ${noRequirement}`;
            document.querySelector('#helpRequirementText').innerHTML = '';
            let help = '';
            help += (data[0].id_evidence == 4) ? `
                <div class="mb-4">
                    Evidencia: ${data[0].document}
                </div>` : '';
            help += `
                <div>
                    ${data[0].help_requirement}
                </div>`;
            $('#helpRequirementText').append(help);
            $('.loading').addClass('d-none');
            $('#helpRequirementModal').modal({backdrop:'static', keyboard: false});
        }
    })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert('Algo salio mal al recuperar la Ayuda', 'error');
    })
}
/**
 *
 */
function aceptanceRequirement(idRequirement, noRequirement){
    $('.loading').removeClass('d-none');
    helpRequirementService(idRequirement)
    .then(data => {
        if(data[0].help_requirement === null){
            $('.loading').addClass('d-none');
            toastAlert('No cuenta con Criterio de aceptación', 'warning');
        }else{
            document.querySelector('#helpRequirementTitle').innerHTML = `Criterio de aceptación de requerimiento: ${noRequirement}`;
            document.querySelector('#helpRequirementText').innerHTML = '';
            let acceptance = '';
            acceptance += `
                <div>
                    ${data[0].acceptance}
                </div>`;
            $('#helpRequirementText').append(acceptance);
            $('.loading').addClass('d-none');
            $('#helpRequirementModal').modal({backdrop:'static', keyboard: false});
        }
    })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert('Algo salio mal al recuperar el Criterio de aceptación', 'error');
    })
}
/**
 * Bsies to requiremnt
 */
function showBasies(idRequirement, noRequirement){
    $('.loading').removeClass('d-none');
    showBasiesServices(idRequirement)
    .then(data => {
        $('#basies tbody').find('tr').remove();
        document.querySelector('#basiesTitle').innerHTML = `Fudamentos legales del requerimiento: ${noRequirement}`;
        if (data.length > 0) {
            document.querySelector('#legalText').innerHTML = '';
            let tbody = '';
            data.forEach(e => {
                tbody += `<div class="row">
                    <div class="col media">
                        <div class="media-body">
                            <h5 class="font-weight-bold font-italic">${e.guideline}</h5>
                            <h5 class="font-weight-bold font-italic">${e.legal_basis}</h5>
                            <div>${e.legal_quote}</div>
                        </div>
                    </div>
                </div>
                <hr class="divider">`;
            });
            $('#legalText').append(tbody);
            $('.loading').addClass('d-none');
            $('#basiesModal').modal({backdrop:'static', keyboard: false});
        }else{
            toastAlert('No cuenta con Fundamentos legales para mostrar', 'info');
        }
        $('.loading').addClass('d-none');
    })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert('No se pudo recuperar los Fundamentos legales', 'error');
    });
}
/**
 * Wizard Initialization
 */
function initWizardBS(){
    return new Promise((resolve, reject) => {
        $('#form-wizard').bootstrapWizard({
            'tabClass': 'nav nav-tabs',
            'nextSelector': '.btn-next',
            'previousSelector': '.btn-back',
            onNext: (tab, navigation, index) => {
                // onNext
            },
            onPrevious: (tab, navigation, index) => {
                // onPrevious
            },
            onInit: (tab, navigation, index) => {
                // onInit
            },
            onTabChange: (tab, navigation, index) => {
                // change
            },
            onTabClick: (tab, navigation, index) => {
                // onTabClick
            },
            onTabShow: (tab, navigation, indexInit) => {
                const stepLink = tab[0].firstElementChild;
                const index = (indexInit < 0 ) ? 0 : indexInit;
                const totalStep = navigation.find('li').length;
                const currentStep = index + 1;
                const wizard = navigation.closest('.card-wizard');
                // If it's the last tab then hide the last button and show the finish instead
                if (currentStep >= totalStep) {
                    $(wizard).find('.btn-next').hide();
                    $(wizard).find('.btn-finish').show();
                } else {
                    $(wizard).find('.btn-next').show();
                    $(wizard).find('.btn-finish').hide();
                }
                // 
                if (currentAspect.totalRequirements == 1) $(wizard).find('.btn-back').hide();
                else $(wizard).find('.btn-back').show();

                if (currentStep == 1) $(wizard).find('.btn-back').hide();
                else $(wizard).find('.btn-back').show();
                // For first tab
                if(index == 0){
                    $(wizard).find('.btn-back').hide();
                    $('#nextButton').removeClass('disabled');
                }
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
        resolve(true)
    });
}
// TODO:
function reloadAttributes($check) {
    const step = document.querySelector('#link-'+idRequirement);
    step.setAttribute('risk-complete', 'yes');
}
/**
 * Evaluate wizard options
 */
function evaluateWizard(){
    const steps = document.querySelector('#quiz-list').getElementsByTagName('a');
    let resWizard = 0;
    for (const s of steps) {
        let countPerStep = evaluateOnlyTab(s, false);
        resWizard += countPerStep;
    }
    currentAspect.validateWizard = resWizard;
}
/*
 * Evaluate per tab of wizard
 */
function evaluateOnlyTab(step, evaluate = true) 
{
    let resWizard = 0;
    const idTab = step.getAttribute('id');
    const idAudit = step.getAttribute('id-audit');
    const risk = step.getAttribute('evaluate-risk');
    const riskComplete = step.getAttribute('risk-complete');
    const finding = step.getAttribute('finding');
    const childs = step.getAttribute('childs-complete');
    step.classList.remove('complete');
    // check answer
    if (idAudit == 'null') {
        $(`#${idTab}-panswer`).removeClass('d-none')
        resWizard++;
    }
    else $(`#${idTab}-panswer`).addClass('d-none')
    // check risk 
    if (risk == 'yes' && riskComplete == 'yes' && currentAR.evaluateRisk) {
        $(`#${idTab}-prisk`).addClass('d-none')
    }
    else if (risk == 'yes' && riskComplete == 'no' && currentAR.evaluateRisk) { 
        $(`#${idTab}-prisk`).removeClass('d-none')
        resWizard++;
    }
    else { // change answer 0 to other
        $(`#${idTab}-prisk`).addClass('d-none');
    }
    // check finding
    if (finding == 'true') {
        $(`#${idTab}-pfinding`).removeClass('d-none')
        resWizard++;
    }
    else $(`#${idTab}-pfinding`).addClass('d-none')
    // check childs
    if (childs == 'false') {
        $(`#${idTab}-child`).removeClass('d-none')
        resWizard++;
    }
    else $(`#${idTab}-child`).addClass('d-none')
    // verify in backend
    if (evaluate) {
        const isCompleted = (resWizard > 0) ? 0 : 1;
        const idRequirement = idTab.split('-').slice(-1).pop();
        setEvaluateRequirement(idRequirement, null, isCompleted)
        .then(data => {
            if (data.status != 'success') toastAlert(data.msg, 'error');
        })
        .catch(e => {
            toastAlert(e, 'error');
        });
    }
    // set class in tab
    if (resWizard > 0) $(`#${idTab}`).addClass('error');
    else {
        $(`#${idTab}`).removeClass('error')
        $(`#${idTab}`).addClass('complete')
    };
    return resWizard;
}
/**
 * set uncomplete in wizard
 */
function setUncomplete(uncomplete){
    const steps = document.querySelector('#quiz-list').getElementsByTagName('a');
    for (const s of steps) {
        const idTab = s.getAttribute('id');
        const idRequirement = idTab.split('-').slice(-1).pop();
        const error = uncomplete.includes(parseInt(idRequirement));
        if (error) {
            $(`#${idTab}`).removeClass('complete')
            $(`#${idTab}`).addClass('error');
            $(`#${idTab}-psub`).removeClass('d-none');
        }
    }
}
/**
/**
 * Remove quiz
 */
function removeQuiz(){
    $('#wizardForm').find('div').remove();
}
/**
 * Finis quiz
 */
function onFinishWizard(){
    if (currentAspect.idStatus == FINISHED) {
        okAlert('Aspecto Finalizado', 'El aspecto solo es de consulta no puede modificarse', 'info');
        return;
    }
    evaluateWizard();
    if ( currentAspect.validateWizard == 0 ) {
        Swal.fire({
            title: `¿Estás seguro de finalizar la auditoria de este aspecto?`,
            text: 'Las preguntas ahora mostradas solo podran ser editables en el futuro',
            icon: 'warning',
            allowOutsideClick: false,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, finalizar!',
            cancelButtonText: 'No, cancelar!'
        }).then((result) => {
            if (result.value) {
                $('.loading').removeClass('d-none');
                setClassifyAspectService()
                .then(data => {
                    if (data.status == 'success') {
                        $('.loading').addClass('d-none');
                        okAlert(data.title, data.msg, data.status);
                        closeAudit();
                    }
                    else {
                        $('.loading').addClass('d-none');
                        okAlert(data.title, data.msg, data.status);
                        setUncomplete(data.uncomplete);
                    }
                })
                .catch(e => {
                    $('.loading').addClass('d-none');
                    toastAlert('Algo salio mal al finalizar el Aspecto', 'error');
                });
            }
        });
    }
    else {
        okAlert('Aún no es posible Terminar la Auditoría de este aspecto', 'Clic sobre los Requerimientos marcados en color rojo y completa lo que se pide', 'error');
    }
}

/***** Section risk *****/

/**
 * Set answer risk
 */
function setRiskAnswer(value, idRequirement, idRiskCategory, idRiskAttribute, idSubrequirement){
    $('.loading').removeClass('d-none');
    if (value == '') {
        $('.loading').addClass('d-none');
        toastAlert('Seleccione una de la respuestas disponibles', 'info');
        return;
    }
    setRiskAnswerServiceV2(value, idRequirement, idRiskCategory, idRiskAttribute, idSubrequirement)
    .then(res => {
        if ( res.hasOwnProperty('info') ) {
            Swal.fire({
                allowOutsideClick: false,
                html: res.info.message,
                icon: 'success',
                confirmButtonText: 'OK',
            });
            if (idSubrequirement != null) {
                return getSubrequirementsAnswers();
            }
            return getAnswersAspect();
        }
        const status = res.success ? 'success' : 'error'
        toastAlert(res.message, status);
        if (idSubrequirement != null) {
            return getSubrequirementsAnswers();
        }
        return getAnswersAspect();
    })
    .then(res => {
        if (idSubrequirement != null) {
            const step = document.querySelector('#linkSub-'+idSubrequirement);
            step.setAttribute('risk-complete', 'yes');
            evaluateSubWizard(step)
            $('.loading').addClass('d-none');
            return;
        }
        const step = document.querySelector('#link-'+idRequirement);
        step.setAttribute('risk-complete', 'yes');
        evaluateOnlyTab(step);
        $('.loading').addClass('d-none');
        return;
    })
    // .then(data => {
    //     if (data.riskTotal != null) {
    //         if (idSubrequirement == null) {
    //             $('#valueRisk-'+idRequirement+'-'+idRiskCategory).removeClass('d-none')
    //             document.querySelector('#valueRisk-'+idRequirement+'-'+idRiskCategory).innerHTML = data.riskTotal;
    //             if (data.totalAnswers == currentAR.totalRisk) {
    //                 const step = document.querySelector('#link-'+idRequirement);
    //                 step.setAttribute('risk-complete', 'yes');
    //                 evaluateOnlyTab(step);
    //             }
    //         }
    //         else {
    //             $('#valueSubRisk-'+idSubrequirement+'-'+idRiskCategory).removeClass('d-none')
    //             document.querySelector('#valueSubRisk-'+idSubrequirement+'-'+idRiskCategory).innerHTML = data.riskTotal;
    //             if (data.totalAnswers == currentAR.totalRisk) {
    //                 const step = document.querySelector('#linkSub-'+idSubrequirement);
    //                 step.setAttribute('risk-complete', 'yes');
    //                 evaluateSubWizard(step)
    //             }
    //         }
    //     }
    //     $('.loading').addClass('d-none');
    //     toastAlert(data.msg, data.status);
    // })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert('Algo salio mal', 'error');
    })
}
/**
 * Open help 
 */
function openHelp(idRiskCategory, idRiskAttribute, attribute) {
    $('.loading').removeClass('d-none');
    let initials = (idRiskAttribute == 1) ? '(P)' : (idRiskAttribute == 2) ? '(E)' : '(C)';
    document.querySelector('#showRiskHelpTitle').innerHTML = `Significado de valores de ${attribute}`;
    document.querySelector('#captionRiskHelpAudit').innerHTML = `Valoración de ${attribute} ${initials}`;
    riskHelpAudit(idRiskCategory, idRiskAttribute)
    .then(data => {
        $('#riskHelpAudit tbody').find('tr').remove();
        let tbody = '';
        if (data.length == 0) {
            tbody += '<th class="text-center font-weight-bold" colspan="3">No hay registros para mostrar</th>';
        }
        else {
            data.forEach(e => {
                tbody += `<tr>
                    <th>${e.risk_help}</th>
                    <th class="text-justify">${e.standard}</th>
                    <th class="text-center">${e.value}</th>
                </tr>`;
            });
        }
        $('#riskHelpAudit tbody').append(tbody);
        $('.loading').addClass('d-none');
        $('#showRiskHelpModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert(e.statusText, 'error');
    });
}
</script>