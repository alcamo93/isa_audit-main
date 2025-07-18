<script>

function getSubrequirements(idRequirement, noRequirement){
    // data for subrequirement
    requirementWithSub.idRequirement = idRequirement;
    requirementWithSub.noRequirement = noRequirement;
    requirementWithSub.validateSubWizard = null;
    // Get subrequirements
    initLoadSubQuiz();
    removeSubQuiz();
    getSubrequirementsServices()
    .then(data => {
        if (data.subReq.length > 0) {
            requirementWithSub.totalSubrequirements = data.subReq.length;
            drawFormSubWizard()
            .then(res => drawSubQuiz(data.subReq, data.risk) )
            .then(res => initSubWizardBS() )
            .then(res => getSubrequirementsAnswers() )
            .then(res => { 
                $('.sub-audit-card').removeClass('d-none');
                $('.loading').addClass('d-none');
            })
            .catch(e => {
                cancelLoadSubQuiz();
                okAlert('Algo salio mal al cargar la evaluación', 'error');
            });
        }else{
            cancelLoadSubQuiz();
            document.querySelector('#link-'+idRequirement).setAttribute('next', 'continue');
            $('input[name=audit'+idRequirement+'][value=2]').prop('checked', true); 
            setAnswer(idRequirement, 2, null);
            setTimeout( () => {
                okAlert('Sin Subrequerimientos', 'Por alguna razón este requerimiento no cuenta con subrequerimientos, por favor continue', 'warning');
            }, 1000);
        }
    })
    .catch(e => {
        cancelLoadSubQuiz();
        okAlert(e, 'error');
    });
}
/**
 * hide main class
 */
function initLoadSubQuiz(){
    $('.loading').removeClass('d-none');
    $('.audit-card').addClass('d-none');
}
/**
 * show main class
 */
function cancelLoadSubQuiz(){
    $('.loading').addClass('d-none');
    $('.audit-card').removeClass('d-none');
}
/**
 * close sub quiz
 */
function closeSubAudit(){
    $('.loading').removeClass('d-none');
    $('.sub-audit-card').addClass('d-none');
    evaluateAllSubWizard();
    if (requirementWithSub.validateSubWizard == 0) {
        setAnswerParent(false)
    }
    else {
        const stepParent = document.querySelector('#link-'+requirementWithSub.idRequirement);
        stepParent.setAttribute('childs-complete', false);
        evaluateOnlyTab(stepParent, false);
    }
    setTimeout( () => {
        $('.loading').addClass('d-none');
        $('.audit-card').removeClass('d-none');
    }, 1000);
}
/**
 * Remove quiz
 */
function removeSubQuiz(){
    $('#wizardSubForm').find('div').remove();
}
/**
 * Control wizard
 */
function controlSubWizard(action, move = 0){
    if (move == 0) {
        $('#form-sub-wizard').bootstrapWizard(action);
        document.querySelector("#prevSubButton").disabled = true;
        document.querySelector("#nextSubButton").disabled = true;
        setTimeout(function(){
            document.querySelector("#prevSubButton").disabled = false;
            document.querySelector("#nextSubButton").disabled = false;
        }, 500);
    }else{
        $('#form-sub-wizard').bootstrapWizard(action, move);
    } 
}
/**
 * Draw content sub wizard
 */
function drawFormSubWizard(){
    return new Promise((resolve, reject) => {
        let html = '';
        html += `<div id="form-sub-wizard" class="card-body">
                    <ul class="wizard-list" id="quiz-sub-list"></ul>
                    <div id="btnSteps" class="card-footer text-center">
                    <button type="button" id="prevSubButton" class="btn btn-success btn-wd btn-sub-back pull-left" onclick="controlSubWizard('previous')">Anterior</button>
                    <button type="button" id="nextSubButton" class="btn btn-success btn-wd btn-sub-next pull-right" onclick="controlSubWizard('next')">Siguiente</button>`;
            if (currentAspect.idStatus != AUDITED) {
                html += `<button type="button" class="btn btn-success btn-wd btn-sub-finish pull-right" onclick="onFinishSubWizard()">Finalizar<i class="fa fa-check-circle fa-lg"></i></button>`;
            }           
                html += `<div class="clearfix"></div>
                    </div>`;
        html += `<div class="tab-content" id="quiz-sub-list-tab"></div>
            </div>`;
        document.getElementById('wizardSubForm').insertAdjacentHTML('beforeend', html); 
        resolve(true);
    });
}
/**
 * Draw list requirements
 */
function drawSubQuiz(dataSubrequirements, dataRisk){
    return new Promise((resolve, reject) => {
        $('#subContent').find('div').remove();
        dataSubrequirements.forEach( (sub, list) => {
            let quizlist = `<li class="wizard-element">
                            <a class="nav-link ${(list != 0) ? '' : 'active'}" href="#subTab${sub.id_subrequirement}" 
                            id="linkSub-${sub.id_subrequirement}"
                            id-audit="null"
                            evaluate-risk="no"
                            risk-complete="no"
                            finding="null"
                            riskWarning="no"
                            data-toggle="tab" role="tab" aria-controls="subTab${sub.id_subrequirement}" 
                            aria-selected="true">${(list+1)}</a>
                        </li>`;
            document.getElementById('quiz-sub-list').insertAdjacentHTML('beforeend', quizlist);
            let quizTab = `<div class="tab-pane fade show ${(list != 0) ? '' : 'active'}" id="subTab${sub.id_subrequirement}" role="tabpanel">
                    <div class="row d-flex justify-content-center">
                            <div class="col-sm-10 col-md-9">
                            <p id="linkSub-${sub.id_subrequirement}-panswer" class="d-none text-center text-danger">Aún no se ha elegido una respuesta</p>
                            <p id="linkSub-${sub.id_subrequirement}-prisk" class="d-none text-center text-danger">Esta pendiente evaluar la sección de Nivel de Riesgo</p>
                            <p id="linkSub-${sub.id_subrequirement}-pfinding" class="d-none text-center text-danger">
                                Es necesario agregar un Hallazgo en este requerimiento ( <i class="fa fa-pencil"></i> )
                            </p>
                        </div>
                    </div>
                    <p class="font-weight-bold text-center">${requirementWithSub.noRequirement}</p>
                    <p class="font-weight-bold text-center">
                        ${sub.no_subrequirement}
                        ${(sub.subrequirement === null ) ? '' : sub.subrequirement }
                    </p>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive"> 
                                <table id="subDataTable-${sub.id_subrequirement}" class="table table-striped table-hover" cellspacing="0" width="100%">
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
                                            <th class="text-justify">${(sub.description === null ) ? '' : sub.description }</th>
                                            <th class="text-center">${(sub.condition === null ) ? '' : sub.condition }</th>
                                            <th class="text-center">${sub.application_type}</th>
                                            <th class="text-center td-actions">
                                                <a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Fundamentos legales" 
                                                    onclick="showBasiesSub(${sub.id_subrequirement}, 'No. ${requirementWithSub.noRequirement} - ${sub.no_subrequirement}')">
                                                    <i class="fa fa-list-ol fa-lg"></i>
                                                </a>
                                                <a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Criterio de aceptación" 
                                                        onclick="aceptanceSubrequirement(${sub.id_subrequirement}, 'No. ${requirementWithSub.noRequirement} - ${sub.no_subrequirement}')">
                                                        <i class="fa fa-check-square fa-lg"></i>
                                                    </a>
                                                <a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Registrar Hallazgo" 
                                                    onclick="showFinding(${requirementWithSub.idRequirement}, 'No. ${requirementWithSub.noRequirement} - ${sub.no_subrequirement}', ${sub.id_subrequirement})">
                                                    <i class="fa fa-pencil fa-lg"></i>
                                                </a>
                                                <a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ayuda para el auditor" 
                                                    onclick="helpSubrequirement(${sub.id_subrequirement}, 'No. ${requirementWithSub.noRequirement} - ${sub.no_subrequirement}')">
                                                    <i class="fa fa-question-circle fa-lg"></i>
                                                </a>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                
                    <fieldset>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-12 text-center control-label">Respuesta</label>
                                <div class="col-12 text-center">
                                    <div class="form-check form-check-radio my-radio">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="radio"
                                                data-rule-required="true" data-msg-required="Seleccione una de la respuestas disponibles" 
                                                name="subAudit${sub.id_subrequirement}" value="0"
                                                onclick="setAnswer(${requirementWithSub.idRequirement}, this.value, ${sub.id_subrequirement})">
                                            <span class="form-check-sign"></span>
                                            No Cumple
                                        </label>
                                    </div>
                                    <div class="form-check form-check-radio my-radio">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="radio"
                                                name="subAudit${sub.id_subrequirement}" value="1"
                                                onclick="setAnswer(${requirementWithSub.idRequirement}, this.value, ${sub.id_subrequirement})">
                                            <span class="form-check-sign"></span>
                                            Cumple
                                        </label>
                                    </div>
                                    <div class="form-check form-check-radio my-radio">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="radio"
                                                name="subAudit${sub.id_subrequirement}" value="2"
                                                onclick="setAnswer(${requirementWithSub.idRequirement}, this.value, ${sub.id_subrequirement}, ${list == 0 ? true : false})">
                                            <span class="form-check-sign"></span>
                                            No Aplica
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>`;
            if (currentAR.evaluateRisk) {
                quizTab += `<div class="row d-none" id="sectionSubRisk-${sub.id_subrequirement}">`;
                dataRisk.forEach((cat, i) => {
                                quizTab += `
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <h5 class="title text-center">${cat.risk_category}</h5>
                                    <h6 
                                        class="title text-center badge-success d-none"
                                        name="interpretationSubRisks-${sub.id_subrequirement}"
                                        id="valueSubRisk-${sub.id_subrequirement}-${cat.id_risk_category}">
                                    </h6>`;
                        cat.attributes.forEach(att => {
                        quizTab += `<div class="row">
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
                                                    id="risk-sub${sub.id_subrequirement}-c${cat.id_risk_category}-a${att.id_risk_attribute}"
                                                    name="inputSelectSubRisks${sub.id_subrequirement}"
                                                    class="form-control"
                                                    data-rule-required="true"
                                                    data-msg-required="Selecciona una opción"
                                                    onchange="setRiskAnswer(this.value, ${requirementWithSub.idRequirement}, ${cat.id_risk_category}, ${att.id_risk_attribute}, ${sub.id_subrequirement})"
                                                >
                                                    <option value=""></option>`;
                                    att.valuations.forEach(val => {
                                        quizTab += `<option value="${val.value}">${val.risk_help}</option>`
                                    });   
                                    quizTab += `</select>
                                            </div>
                                        </div>
                                    </div>`;
                                });
                    quizTab += `</div>`;
                            });
                quizTab += `</div>`;
            }
            quizTab += `</div>`;
            document.getElementById('quiz-sub-list-tab').insertAdjacentHTML('beforeend', quizTab);
            // $('#quiz-sub-list-tab').append(quiz);
        });
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
        resolve(true)
    })
}
/**
 * Get subrequirements answers
 */
function getSubrequirementsAnswers(){
    return new Promise((resolve, reject) => {
        $.get('/audit/requirement/answers/sub', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idRequirement: requirementWithSub.idRequirement, 
            idAuditAspect: currentAspect.idAuditAspect
        },
        (data) => {
            if (data.length > 0) {
                data.forEach(res => {
                    const findingAttr = (res.finding != null) ? true : false;
                    const isNegative = (res.answer == 0) ? true : false;
                    const needFinding = !findingAttr;
                    const requireFinding = (isNegative) ? needFinding : false;
                    const step = document.querySelector('#linkSub-'+res.id_subrequirement);
                    // Values answers
                    $('input[name=subAudit'+res.id_subrequirement+'][value='+res.answer+']').prop('checked', true); 
                    step.setAttribute('id-audit', res.id_audit);
                    step.setAttribute('finding', requireFinding);
                    if (isNegative && currentAR.evaluateRisk) {
                        step.setAttribute('evaluate-risk', 'yes');
                        if (res.risk_answers.length == currentAR.totalRisk) {
                            step.setAttribute('risk-complete', 'yes');
                        }
                        // show section risk level
                        $('#sectionSubRisk-'+res.id_subrequirement).removeClass('d-none');
                        // set warning in change answer if risk enabled
                        step.setAttribute('riskWarning', 'yes');
                        // set properties risk
                        res.risk_answers.forEach(ris => {
                            let id = `risk-sub${res.id_subrequirement}-c${ris.id_risk_category}-a${ris.id_risk_attribute}`;
                            document.getElementById(id).value = ris.answer;
                        });
                        // set total 
                        res.risk_totals.forEach(j => {
                            $('#valueSubRisk-'+res.id_subrequirement+'-'+j.id_risk_category).removeClass('d-none')
                            document.querySelector('#valueSubRisk-'+res.id_subrequirement+'-'+j.id_risk_category).innerHTML = j.interpretation;
                        });
                    }
                    evaluateSubWizard(step, false)
                });
                // Show advance in contract aspect
                requirementWithSub.totalAnswers = document.querySelector('#quiz-list').getElementsByClassName('complete').length;
                if (requirementWithSub.totalSubrequirements == requirementWithSub.totalAnswers){
                    if (currentAspect.idStatus != AUDITED) {
                        // okAlert('Editar Subrequerimiento', 'Da clic en finalizar para evaluar el subrequerimiento', 'info');
                    }else{
                        toastAlert('Ya puedes finalizar la evaluación del subrequerimiento', 'info', 5000);
                        // controlWizard('next', requirementWithSub.totalAnswers);
                    }
                }
            }
            resolve(true)
        })
        .fail(e => {
            reject(e.statusText);
        });
    });
}
/**
 * Help to audit
 */
function helpSubrequirement(idSubrequirement, noSubrequirement){
    $('.loading').removeClass('d-none');
    helpSubrequirementService(idSubrequirement)
    .then(data => {
        if(data[0].help_subrequirement === null){
            $('.loading').addClass('d-none');
            toastAlert('No cuenta con ayuda', 'warning');
        }else{
            document.querySelector('#helpRequirementTitle').innerHTML = `Ayuda para requerimiento: ${noSubrequirement}`;
            $('#helpRequirementText').find('div').remove();
            let help = '';
            help += (data[0].id_evidence == 4) ? `
                <div class="mb-4">
                    Evidencia: ${data[0].document}
                </div>` : '';
            help += `
                <div>
                    ${data[0].help_subrequirement}
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
function aceptanceSubrequirement(idSubrequirement, noSubrequirement){
    $('.loading').removeClass('d-none');
    helpSubrequirementService(idSubrequirement)
    .then(data => {
        if(data[0].help_subrequirement === null){
            $('.loading').addClass('d-none');
            toastAlert('No cuenta con Criterio de aceptación', 'warning');
        }else{
            document.querySelector('#helpRequirementTitle').innerHTML = `Criterio de aceptación para requerimiento: ${noSubrequirement}`;
            $('#helpRequirementText').find('div').remove();
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
        toastAlert('Algo salio mal al recuperar la Ayuda', 'error');
    })
}
/**
 * Bsies to subrequiremnt
 */
function showBasiesSub(idSubrequirement, noSubrequirement){
    $('.loading').removeClass('d-none');
    showBasiesSubServices(idSubrequirement)
    .then(data => {
        $('#basies tbody').find('tr').remove();
        document.querySelector('#basiesTitle').innerHTML = `Fudamentos legales del requerimiento: ${noSubrequirement}`;
        if (data.length > 0) {
            $('#legalText').find('p').remove();
            $('#legalText').find('hr').remove();
            $('#legalText').find('div').remove();
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
 * Sub Wizard Initialization
 */
function initSubWizardBS(){
    return new Promise((resolve, reject) => {
        $('#form-sub-wizard').bootstrapWizard({
            'tabClass': 'nav nav-tabs sub',
            'nextSelector': '.btn-sub-next',
            'previousSelector': '.btn-sub-back',
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
                const total = navigation.find('li').length;
                const index = (indexInit < 0 ) ? 0 : indexInit;
                const current = index + 1;
                const wizard = navigation.closest('.card-wizard.sub');
                // If it's the last tab then hide the last button and show the finish instead
                if (current >= total) {
                    $(wizard).find('.btn-sub-next').hide();
                    $(wizard).find('.btn-sub-finish').show();
                } else {
                    $(wizard).find('.btn-sub-next').show();
                    $(wizard).find('.btn-sub-finish').hide();
                }
                if (current == 1) {
                    $(wizard).find('.btn-sub-back').hide();
                }else{
                    $(wizard).find('.btn-sub-back').show();
                }
                // For first tab
                if(index == 0){
                    $(wizard).find('.btn-sub-back').hide();
                    $('#nextSubButton').removeClass('disabled');
                }
                // Progress bar
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
        resolve(true)
    });
}
/**
 * Evaluate wizard options
 */
function evaluateAllSubWizard(){
    const steps = document.querySelector('#quiz-sub-list').getElementsByTagName('a');
    let resWizard = 0;
    for (const s of steps) {
        let countPerStep = evaluateSubWizard(s, false)
        resWizard += countPerStep;
    }
    requirementWithSub.validateSubWizard = resWizard;
    if (resWizard == 0) {
        $(`#${requirementWithSub.idRequirement}-psub`).removeClass('d-none');
    }
}
/**
 * Evaluate per tab of wizard
 */
function evaluateSubWizard(step, evaluate = true){
    let resWizard = 0;
    const idTab = step.getAttribute('id');
    const idAudit = step.getAttribute('id-audit');
    const risk = step.getAttribute('evaluate-risk');
    const riskComplete = step.getAttribute('risk-complete');
    const finding = step.getAttribute('finding');
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
    // verify in backend
    if (evaluate) {
        const isCompleted = (resWizard > 0) ? 0 : 1;
        const idRequirement = requirementWithSub.idRequirement;
        const idSubrequirement = idTab.split('-').slice(-1).pop();
        setEvaluateRequirement(idRequirement, idSubrequirement, isCompleted)
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
 * Finis quiz
 */
function onFinishSubWizard(){
    evaluateAllSubWizard()
    if(requirementWithSub.validateSubWizard == 0) {
        setAnswerParent()
    }
    else {
        okAlert('Aún no es posible evaluar el Requerimiento', 'Clic sobre los Subrequerimientos en color rojo y completa lo que se pide', 'error');
    }
}

function setAnswerParent( close = true ) {
    return evaluateReqComposite()
    .then(data => {
        if (data.riskStatus == 'success') {
            toastAlert(data.statusAnswer.msg, data.statusAnswer.status); 
            if (data.statusAnswer.status == 'success') {
                // set properties to parent requirement 
                $('input[name=audit'+data.idRequirement+'][value='+data.answer+']').prop('checked', true); 
                const step = document.querySelector('#link-'+data.idRequirement);
                step.setAttribute('id-audit', data.statusAnswer.idAudit);
                step.setAttribute('childs-complete', true);
                evaluateOnlyTab(step)
                if (close) closeSubAudit();
            }    
        }
        else {
            okAlert(data.statusAnswer.title, data.statusAnswer.msg, 'warning');
        }
    })
    .catch(e =>{
        toastAlert(e, 'error');
    })
}
</script>