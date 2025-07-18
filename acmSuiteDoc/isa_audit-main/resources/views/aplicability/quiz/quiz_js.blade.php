<script>
$(document).ready( () => {
    setFormValidation('#setCommentsForm');
});
/*************************** Quiz to Aplicability ***************************/
/**
 * Open quiz by aspect
 */
function openQuiz(idAspect, matter, aspect, idContractAspect, idStatus, idContractMatter, idAplicabilityRegister, idForm) {
    // Set global data
    currentAspect.idForm = idForm;
    currentAspect.idAspect = idAspect;
    currentAspect.idContractAspect = idContractAspect;
    currentAspect.idStatus = idStatus;
    currentAspect.matter = matter;
    currentAspect.aspect = aspect;
    currentAspect.validateWizard = null;
    // Show screen wizard
    initLoadQuiz();
    document.querySelector('#quizMatterTitle').innerHTML = currentAspect.matter;
    document.querySelector('#quizAspectTitle').innerHTML = currentAspect.aspect;
    removeQuiz();
    // Get questions for status aspects
    getQuestionByAspect()
    .then(data => {
        if (data.status == 'error') {
            cancelLoadQuiz();
            okAlert('Sin Dirección Física', 'Por favor complete los datos de dirección de esta planta', 'warning');
            return;
        }
        if (data.questions.length != 0 && data.status == 'success') {
            currentAspect.totalQuestions = data.questions.length;
            currentAspect.dependency = data.dependency;
            currentAspect.freeDependency = data.freeDependency;
            drawFormWizard()
            .then(res => drawQuiz(data.questions) )
            .then(res => {
                getAllQuestionInForm();
                return initWizardBS();
            })
            .then(res => getAnswersAspect() )
            .then(res => {
                $('.loading').addClass('d-none');
                $('.question-card').removeClass('d-none');
            })
            .catch(e => {
                cancelLoadQuiz();
                toastAlert('Error al cargar cuestionario', 'error');
            });
        }else{
            cancelLoadQuiz();
            removeAspectVoid(aspect, idContractMatter, idContractAspect, idAplicabilityRegister);
        }
    })
    .catch(e => {
        cancelLoadQuiz();
        toastAlert(e, 'error');
    });
}
/**
 * Remove void aspect 
 */
function removeAspectVoid(aspectName, idContractMatter, idContractAspect, idAplicabilityRegister) {
    Swal.fire({
        title: `Sin cuestionario para aspecto: "${aspectName}"`,
        text: `Para este aspecto no hay cuestionarios cargados, ¿Desea eliminar el aspecto de la auditoría?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            // send to server
            $.post('/aplicability/resgisters/aspect/delete', {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idContractMatter: idContractMatter,
                idContractAspect: idContractAspect
            },
            data => {
                $('.loading').addClass('d-none');
                toastAlert(data.msg, data.status);
                getContractMatters(idAplicabilityRegister)
                .then(()=>{
                    reloadaspectsRegister();
                })
                .catch(()=>{
                    toastAlert('No se pudo obtener las materias', 'error');
                });
            })
            .fail(e=> {
                $('.loading').addClass('d-none');
                toastAlert(e.statusText, 'error');
            });
        }
    });
}
/**
 * hide main class
 */
function initLoadQuiz(){
    $('.loading').removeClass('d-none');
    $('.matters-list-card').addClass('d-none');
    $('body').addClass('sidebar-mini');
}
/**
 * show main class
 */
function cancelLoadQuiz(){
    $('.loading').addClass('d-none');
    $('.matters-list-card').removeClass('d-none');
    // $('body').removeClass('sidebar-mini');
}
/**
 * Remove quiz
 */
function removeQuiz(){
    $('#wizardForm').find('div').remove();
}
/**
 * close quiz
 */
function closeQuiz(){
    reloadaspectsRegister();
    $('.loading').removeClass('d-none');
    $('.question-card').addClass('d-none');
    // $('body').removeClass('sidebar-mini');
    setTimeout(function(){
        $('.loading').addClass('d-none');
        $('.matters-list-card').removeClass('d-none');
    }, 1000);
}
/**
 * Draw form wizard
 */
function drawFormWizard(){
    return new Promise ((resolve, reject) => {
        let html = '';
        html += `<div id="form-wizard" class="card-body" >
                    <ul class="wizard-list" id="quiz-list">
                    </ul> 
                    <div id="btnSteps" class="card-footer text-center">
                        <button type="button" id="prevButton" class="btn btn-success btn-wd btn-back pull-left" onclick="controlWizard('previous')">Anterior</button>
                        <button type="button" id="nextButton" class="btn btn-success btn-wd btn-next pull-right" onclick="controlWizard('next')">Siguiente</button>`;
            if (currentAspect.idStatus != CLASSIFIED) { // se quito por la repetición de revisión de bloqueo de preguntas
                html += `<button type="button" id="finishButtton" class="btn btn-success btn-wd btn-finish pull-right" onclick="onFinishWizard()">Finalizar</button>`;
            }           
                html += `<div class="clearfix"></div>
                    </div>`;

                html += `<div class="tab-content" id="quiz-list-tab"></div>
                    </div>`;
        document.querySelector('#wizardForm').innerHTML= html;
        resolve(true);
    });
}
/**
 * Draw quiz
 */
function drawQuiz(questions) {
    return new Promise ((resolve, reject) => {
        questions.forEach( (q, index, allQuestions) => {
            let quizlist = `
                        <li class="wizard-element">
                            <a class="nav-link ${(index != 0) ? '' : 'active'}" href="#tab${q.id_question}" 
                                data-toggle="tab" role="tab" aria-controls="tab${q.id_question}" 
                                id="question-${q.id_question}"
                                has-been-answered="false"
                                id-app="false"
                                skip="false"
                                contain-multi-answer="false"
                                multi-answer-complete="false"
                                first="${ ( index == 0 ) ? 'true' : 'false' }"
                                last="${ ( (currentAspect.totalQuestions - 1) == index ) ? 'true' : 'false' }"
                                aria-selected="true"
                            >
                                ${(index+1)}
                            </a>
                        </li>`;
            document.getElementById('quiz-list').insertAdjacentHTML('beforeend', quizlist);
            const isFreeBlocked = currentAspect.freeDependency.some(e => e === q.id_question);
            let quizTabs = `
                    <div class="tab-pane fade show ${(index != 0) ? '' : 'active'}" id="tab${q.id_question}" role="tabpanel">
                        <div class="row d-flex justify-content-center">
                            <div class="col-sm-10 col-md-9">
                                <p id="question-${q.id_question}-panswer" class="d-none text-center text-danger">Aún no se ha elegido una respuesta</p>
                                <p id="question-${q.id_question}-pblock" class="${(index == 0 || isFreeBlocked) ? 'd-none' : ''} text-center text-danger">
                                    Pregunta bloqueada, ${(index === allQuestions.length - 1) ? 'finaliza el formulario' : 'pasa a la siguiente pregunta'}
                                </p>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center" style="height-max: calc(100vh - 520px); overflow-y: auto;">
                            <div class="col-sm-10 col-md-9">
                                <p class="text-justify">${q.question}</p>
                            </div>
                        </div>
                        <fieldset>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-center">
                                        <span class="checkbox mr-2">Respuesta</span>`;
                                        if (q.help_question != null) {
                                        quizTabs += 
                                        `<button type="button" class="close my-close pull-right" data-toggle="tooltip" 
                                            title="Ayuda" onclick="helpQuestion(${q.id_question}, '${(index+1)}/${currentAspect.totalQuestions}');">
                                            <i class="fa fa-info-circle"></i>
                                        </button>
                                        <button type="button" class="close my-close pull-right ml-2" data-toggle="tooltip" 
                                            title="Fundamentos Legales" onclick="legalsQuestion(${q.id_question}, '${(index+1)}/${currentAspect.totalQuestions}');">
                                            <i class="fa fa-list-ol"></i>
                                        </button>
                                        <button type="button" class="close my-close pull-right ml-2" data-toggle="tooltip" 
                                            title="Comentarios" onclick="addComments(${q.id_aplicability}, '${(index+1)}/${currentAspect.totalQuestions}', '${q.comments}');">
                                            <i class="fa fa-pencil-square"></i>
                                        </button>`;
                                            
                                        }
                                        quizTabs += `
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col">`;
                                    const classAllow = (q.allow_multiple_answers == 0) ? 'form-check-radio' : '';
                                    const typeAllow = (q.allow_multiple_answers == 0) ? 'radio' : 'checkbox';
                                    const disableAll = currentAspect.idStatus == FINISHED
                                    const disableQuestion = (index == 0 || isFreeBlocked) ? '' : 'disabled';
                                    const putDisabledQuestion = disableAll ? 'disabled' : disableQuestion;
                                    q.answers_question.forEach((e, i) => {
                                    const sameName = (q.allow_multiple_answers == 0) ? '' : `-${e.id_answer_question}`;
                                    quizTabs += `
                                    <div class="form-check ${classAllow} d-flex justify-content-center">
                                        <label class="form-check-label">
                                            <input class="form-check-input question-${e.id_question}" 
                                                type="${typeAllow}" ${putDisabledQuestion}
                                                name="multiAnswerCheck-${e.id_question}${sameName}"
                                                value="${e.id_answer_value}"
                                                idAQ="${e.id_answer_question}"
                                                onclick="setAnswer(${q.id_question}, ${e.id_answer_question}, this.value, this.checked)"
                                            >
                                            <span class="form-check-sign"></span>
                                            <span style="text-transform: none;">${e.description}</span>
                                        </label>
                                    </div>`;
                                    });
                                quizTabs += `
                                </div>
                            </div>
                        </fieldset>
                    </div>`;
            document.getElementById('quiz-list-tab').insertAdjacentHTML('beforeend', quizTabs);
        });
        resolve(true)
    });
}
/**
 * Wizard Initialization
 */
function initWizardBS(){
    return new Promise ((resolve, reject) => {
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
            onTabClick: (tab, navigation, index) => {
                // onTabClick
            },
            onLast: (tab, navigation, index) => {
                // onLast
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
                // Only question
                if (currentAspect.totalQuestions == 1) $(wizard).find('.btn-back').hide();
                else $(wizard).find('.btn-back').show();
                // For first tab
                if(index == 0){
                    $(wizard).find('.btn-back').hide();
                    $('#nextButton').removeClass('disabled');
                }
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
        resolve(true);
    });
}
/**
 * Control wizard
 */
function controlWizard(action, move = 0){
    if (move == 0) {
        $('#form-wizard').bootstrapWizard(action);
        document.querySelector("#prevButton").disabled = true;
        document.querySelector("#nextButton").disabled = true;
        setTimeout(function(){
            document.querySelector("#prevButton").disabled = false;
            document.querySelector("#nextButton").disabled = false;
        }, 500);
    }else{
        $('#form-wizard').bootstrapWizard(action, move);
    } 
}
/**
 * block wizard options
 */
function blockWizard(idAnswerQuestion, idQuestion){
    const steps = document.querySelector('#quiz-list').getElementsByTagName('a');
    const indexEvaluate = currentAspect.dependency.findIndex(e => e.id_answer_question == idAnswerQuestion);
    let blockedIds = [];
    let allowedIds = [];
    if ( indexEvaluate != -1 ) {
        const { allowed, blocked } = currentAspect.dependency[indexEvaluate];
        blockedIds = blocked.map(e => `question-${e}`);
        allowedIds = allowed.map(e => `question-${e}`);   
    }
    for (const s of steps) {
        const idTab = s.getAttribute('id');
        const hasbLocked = blockedIds.includes(idTab);
        if (hasbLocked) {
            $('.'+idTab+':checked').prop('checked', false); 
            s.setAttribute('skip', 'true');
            $('.'+idTab).attr('disabled', true);
            $(`#${idTab}-pblock`).removeClass('d-none')
            $(`#${idTab}`).removeClass('error')
            $(`#${idTab}`).addClass('complete')
        }
        const hasAllowed = allowedIds.includes(idTab);
        if (hasAllowed) {
            s.setAttribute('skip', 'false');
            $('.'+idTab).attr('disabled', false);
            $(`#${idTab}-pblock`).addClass('d-none')
            $(`#${idTab}`).removeClass('error')
            $(`#${idTab}`).removeClass('complete')
        }
    }
}
/**
 * Evaluate wizard options
 */
function evaluateWizard(){
    const steps = document.querySelector('#quiz-list').getElementsByTagName('a');
    let resWizard = 0;
    for (const s of steps) {
        let countPerStep = evaluatePerTab(s);
        resWizard += countPerStep;
    }
    currentAspect.validateWizard = resWizard;
}

function getAllQuestionInForm() {
    const steps = document.querySelector('#quiz-list').getElementsByTagName('a');
    let questions = [];
    for (const s of steps) {
        questions.push( parseInt(s.id.split('-').pop()) );
    }
    const dependency = questions.filter(q => !currentAspect.freeDependency.includes(q));
    currentAspect.questionInForm = questions;
    currentAspect.dependencyInForm = dependency;
}
/*
 * Evaluate per tab of wizard
 */
function evaluatePerTab(step){
    let resWizard = 0;
    const idTab = step.getAttribute('id');
    const idAplicability = step.getAttribute('id-app');
    const skipQuestion = step.getAttribute('skip');
    step.classList.remove('complete');
    // block answers
    if (skipQuestion != 'true') {
        $(`#${idTab}-pblock`).addClass('d-none')
        // check answer
        if (idAplicability == 'false') {
            $(`#${idTab}-panswer`).removeClass('d-none')
            resWizard++;
        }
        else {
            $(`#${idTab}-panswer`).addClass('d-none')
        }
    }
    // set class in tab
    if (resWizard > 0) {
        $(`#${idTab}`).addClass('error');
    }
    else {
        $(`#${idTab}`).removeClass('error')
        $(`#${idTab}`).addClass('complete')
    };

    return resWizard;
}
/**
 * Get answers save previus
 */
function getAnswersAspect(){
    return getAnswersAspectService()
    .then(data => {
        $('.loading').removeClass('d-none');
        if (data.length === 0) {
            currentAspect.questionInForm.forEach(q => {
                $(`#question-${q}`).removeClass('error')
                $(`#question-${q}`).removeClass('complete')
                $(`#question-${q}-pblock`).addClass('d-none');
                $('.question-'+q).attr('disabled', false);
                $('.question-'+q+':checked').prop('checked', false); 
            });
            currentAspect.dependencyInForm.forEach((q, index) => {
                if (index !== 0) {
                    $(`#question-${q}`).removeClass('error')
                    $(`#question-${q}`).removeClass('complete')
                    $(`#question-${q}-pblock`).removeClass('d-none');
                    $('.question-'+q).attr('disabled', true);
                    $('.question-'+q+':checked').prop('checked', false); 
                }
            });
            return;
        }
        const steps = document.querySelector('#quiz-list').getElementsByTagName('a');
        let allQuestions = [];
        for (const s of steps) {
            const idTab = s.getAttribute('id');
            $(`#${idTab}`).removeClass('error')
            $(`#${idTab}`).removeClass('complete')
            $(`#${idTab}-pblock`).addClass('d-none');
            allQuestions.push(idTab)
            $('.'+idTab).attr('disabled', true);
            $('.'+idTab+':checked').prop('checked', false); 
        }
        // Set answers in cuestions
        data.forEach(e => {
            const step = document.querySelector('#question-'+e.id_question);
            const id = step.getAttribute('id');
            const first = step.getAttribute('first');
            const indexForm = allQuestions.findIndex(i => i === id)
            if (indexForm !== -1) {
                allQuestions.splice(indexForm, 1);
            }
            $(`#${id}`).removeClass('error').removeClass('complete');
            step.setAttribute('has-been-answered', 'true');
            step.setAttribute('id-app', e.id_aplicability);
            step.setAttribute('contain-multi-answer', 'true');
            if (e.id_answer_value !== null) {
                const sameName = (e.question.allow_multiple_answers == 0) ? '' : `-${e.id_answer_question}`;
                const nameElements = 'input[name=multiAnswerCheck-'+e.id_question+sameName+'][idAQ='+e.id_answer_question+'][value='+e.id_answer_value+']';
                $(nameElements).prop('checked', true); 
                $('.'+id).attr('disabled', false);
                // blockWizard(e.id_answer_question, e.id_question);
                evaluatePerTab(step);
                if (first === 'false') {
                    $('.'+id).attr('disabled', false);
                }
            }
            else {
                $('.'+id+':checked').prop('checked', false); 
                step.setAttribute('skip', 'true');
                $('.'+id).attr('disabled', true);
                $(`#${id}-pblock`).removeClass('d-none');
                $(`#${id}`).removeClass('error');
                $(`#${id}`).addClass('complete');
                $(`#${id}`).attr('disabled', true);
            }
        });
        // Unlocked
        allQuestions.forEach(id => {
            $('.'+id).attr('disabled', false);
            $(`#${id}-pblock`).addClass('d-none');
        });
        // Show advance in contract aspect
        currentAspect.totalAnswers = document.querySelector('#quiz-list').getElementsByClassName('complete').length;
        if (currentAspect.totalQuestions == currentAspect.totalAnswers){ // se quito por la repetición de revisión de bloqueo de preguntas
            if (currentAspect.idStatus == CLASSIFIED) {
                okAlert('Editar aplicabilidad', 'Se guardaran las respuestas automáticamente, haga clic en "Regresar" al concluir', 'info');
            }else{
                toastAlert('Ya puedes finalizar la evaluación', 'info', 5000);
            }
        }
        $('.loading').addClass('d-none');
    })
    .catch(e => {
        console.error(e);
        toastAlert('Algo salio mal al cargar el avance', 'error');
    });
}
/**
 * Set aplicability answer in each step
 */
function setAnswer(idQuestion, idAnswerQuestion, idAnswerValue, valueCheck){
    if (currentAspect.idStatus == FINISHED) {
        okAlert('Aspecto Finalizado', 'El aspecto solo es de consulta no puede modificarse', 'info');
        return;
    }
    $('.loading').removeClass('d-none');
    setAnswerService(idQuestion, idAnswerQuestion, idAnswerValue, valueCheck)
    .then(data => {
        toastAlert(data.msg, data.status);
        // add count total answer news
        const step = document.querySelector('#question-'+idQuestion); 
        const hasBeenAnswered = step.getAttribute('has-been-answered');
        if (data.status == 'success') {
            step.setAttribute('id-app', data.idAplicability);
            step.setAttribute('has-been-answered', data.wasAnswered);
            if (hasBeenAnswered == 'false') {
                step.setAttribute('has-been-answered', 'true');
                let last = step.getAttribute('last');
                if ( (last == 'true') ) $('#finishButtton').show()
                else $('#finishButtton').hide();
            }
            // delete answers with dependency
            // blockWizard(idAnswerQuestion, idQuestion);
            // evaluatePerTab(step);
        }
    })
    .then(() => getAnswersAspect() )
    .then(() => {
        $('.loading').addClass('d-none');
    })
    .catch(e => {
        $('.loading').addClass('d-none');
        toastAlert(e.statusText, 'error');
    });
}
/**
 * Help question
 */
function helpQuestion(idQuestion, index){
    $('.loading').removeClass('d-none');
    getDataQuestion(idQuestion)
    .then(data => {
        if(data[0].help_question === null) {
            $('.loading').addClass('d-none');
            toastAlert('No cuenta con ayuda', 'warning');
        }
        else {
            document.querySelector('#dataQuestionTitle').innerHTML = `Ayuda para pregunta: ${index}`;
            document.querySelector('#dataQuestionText').innerHTML = '';
            help = `<p class="text-justify">${data[0].help_question}</p>`;
            $('#dataQuestionText').append(help);
            $('.loading').addClass('d-none');
            $('#dataQuestionModal').modal({backdrop:'static', keyboard: false});
        }
    }).catch(e => {
        $('.loading').addClass('d-none');
        toastAlert(e.statusText, 'error');
    })
}
/**
 * Help question
 */
function legalsQuestion(idQuestion, index){
    $('.loading').removeClass('d-none');
    getDataLegalQuestion(idQuestion)
    .then(data => {
        if(data.length === 0) {
            $('.loading').addClass('d-none');
            toastAlert('No cuenta con Fundamentos Legales', 'warning');
        }
        else {
            document.querySelector('#dataQuestionTitle').innerHTML = `Fudamentos Legales para pregunta: ${index}`;
            document.querySelector('#dataQuestionText').innerHTML = '';
            let legals = '';
            data.forEach(e => {
                legals += `<div id="sectionBodyModal" class="row">
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
            $('#dataQuestionText').append(legals);
            $('.loading').addClass('d-none');
            $('#dataQuestionModal').modal({backdrop:'static', keyboard: false});
        }
    }).catch(e => {
        $('.loading').addClass('d-none');
        toastAlert(e.statusText, 'error');
    })
}
/**
 * Add comments
 */
function addComments(idAplicability, index, comments) {
    if (!idAplicability) {
        toastAlert('Primero selecciona una respuesta para agregar comentarios', 'error');
        return;
    }
    document.querySelector('#setCommentsForm').reset();
    $('#setCommentsForm').validate().resetForm();
    $('#setCommentsForm').find(".error").removeClass("error");
    $('.loading').removeClass('d-none');
    document.querySelector('#dataCommentsTitle').innerHTML = `Comentarios: ${index}`;
    document.querySelector('#dataCommentsTitle').setAttribute('id-aplicability', idAplicability)
    $('.loading').addClass('d-none');
    $('#dataCommentsModal').modal({backdrop:'static', keyboard: false});
    if(comments != 'null'){
        $('#comments').val(comments);
    }
}

$('#setCommentsForm').submit( (event) => {
    event.preventDefault();
    if($('#setCommentsForm').valid()){
        $('.loading').removeClass('d-none');
        const comment = document.querySelector('#comments').value;
        const idAplicability = document.querySelector('#dataCommentsTitle').getAttribute('id-aplicability');
        setComments(idAplicability, comment)
        .then(data => {
            toastAlert(data.msg, data.status);
            $('.loading').addClass('d-none');
            $('#dataQuestionModal').modal('hide');
        }).catch(e => {
            $('.loading').addClass('d-none');
            toastAlert(e.statusText, 'error');
        })
    }
})

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
            title: `¿Estás seguro de finalizar la aplicabilidad de este aspecto?`,
            text: 'Las preguntas ahora mostradas solo podran ser editables en el futuro',
            icon: 'warning',
            allowOutsideClick: false,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, finalizar!',
            cancelButtonText: 'No, cancelar!'
        }).then(result => {
            if (result.value) {
                setClassifyAspect()
                .then(data => {
                    if (data.status == 'success') {
                        okAlert(data.title, data.msg, data.status);
                        console.table(data.log);
                        closeQuiz();
                    } 
                    else toastAlert(data.msg, data.status);
                })
                .catch(e => {
                    toastAlert(e, 'error');
                });
            }
        });
    }
    else {
        okAlert('Aún no es posible Terminar la Aplicabilidad del aspecto', 'Navega por las preguntas y completo lo que se pide', 'error');
    }
}

function helpQuiz() {
    okAlert('Ayuda', 'A continuación, lee y responde cada una de las preguntas', 'question')
}
</script>
