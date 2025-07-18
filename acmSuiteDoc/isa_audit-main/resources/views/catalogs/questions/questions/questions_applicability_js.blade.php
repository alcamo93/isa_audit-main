<script>
$(document).ready( () => {
    setFormValidation('#questionForm');
    setFormValidation('#setAnswerForm');
    const toolbar = {
        dialogsInBody: true,
        dialogsFade: true,
        lang: 'es-ES',
        placeholder: 'Especifica el formato del texto del artículo',
        tabsize: 2,
        height: 200,
        minHeight: null,
        maxHeight: null, 
        toolbar: [
            ['font', ['bold', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
        ]
    }
    $('#helpQuestion').summernote(toolbar);
});
/*************** Active menu ***************/
activeMenu(8, 'Cuestionario de aplicabilidad');

/*****************************************************************************************
*   Question selection functions, sets filter opctions for requirement/basis selection ***  
*****************************************************************************************/
/**
*   information of selected question
*/
const selection = {
    matter: null,
    aspect: null,
    idRequirementType: null,
    idQuestionType: null,
    idApplicationType: null,
    subrequirementType: null,
    idState: null,
    idCity: null,
    aplicationType: null
}
const constQuestionTypes = {
    // table: c_question_types
    federal: 1,
    state: 2,
    unlock: 3,
    local: 4
}
const constApplicationTypes = {
    // table: c_application_types
    federal: 1,
    state: 2,
    noApply: 3,
    local: 4
}
const constRequirementTypes = {
    // table: c_requirement_types only selectable
    identificationFederal: 1,
    identificationState: 2,
    identificationLocal: 13,
    requirementCompositeIdentification: 17
}
const constSubrequirementTypes = {
    // table: c_requirement_types only selectable
    identificationFederal: 6,
    identificationState: 8,
    identificationLocal: 15
}
/**
* Pick question and sets info
*/
function selectQuestion(idQuestion) {
    let index = questions.data.findIndex( o => o.id_question === idQuestion );
    selection.matter = questions.data[index].id_matter;
    selection.aspect = questions.data[index].id_aspect;
    selection.idQuestionType = questions.data[index].id_question_type;
    selection.idState = questions.data[index].id_state;
    selection.idCity = questions.data[index].id_city;
    // Define filters in requirements
    switch (selection.idQuestionType) {
        case constQuestionTypes.federal:
            selection.idApplicationType = constApplicationTypes.federal;
            selection.idRequirementType = [
                constRequirementTypes.requirementCompositeIdentification,
                constRequirementTypes.identificationFederal
            ];
            selection.subrequirementType = constSubrequirementTypes.identificationFederal;
        break;
        case constQuestionTypes.state:
            selection.idApplicationType = constApplicationTypes.state;
            selection.idRequirementType = [
                constRequirementTypes.requirementCompositeIdentification,
                constRequirementTypes.identificationState
            ];
            selection.subrequirementType = constSubrequirementTypes.identificationState;
        break;
        case constQuestionTypes.unlock:
            selection.idApplicationType = null;
            selection.idRequirementType = [null, null];
            selection.subrequirementType = null;
        break;
        case constQuestionTypes.local:
            selection.idApplicationType = constApplicationTypes.local;
            selection.idRequirementType = [
                constRequirementTypes.requirementCompositeIdentification, 
                constRequirementTypes.identificationLocal
            ];
            selection.subrequirementType = constSubrequirementTypes.identificationLocal;
        break;
    }
}
/**
* Clear question data
*/
function clearQuestionSelection() {
    selection.matter = null
    selection.aspect = null
    selection.idQuestionType = null
    selection.idRequirementType = null
    selection.idApplicationType = null
    selection.subrequirementType = null
}
/**
* Questions datatable
 */
const questionTable = $('#questionTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/questions',
        type: 'POST',
        data:  data => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.filterName = document.querySelector('#filterQuestion').value,
            // data.idMatter = document.querySelector('#filterIdMatter').value,
            // data.idAspect = document.querySelector('#filterIdAspect').value,
            data.idQuestionType = document.querySelector('#filterIdQuestionType').value,
            data.idState = document.querySelector('#filterIdState').value,
            data.idCity = document.querySelector('#filterIdCity').value,
            data.form_id = '{{ $parameters->id }}'
        }
    },
    columns: [
        { data: 'order', className: 'td-actions text-center', width:100, orderable : true },
        { data: 'question', orderable : true },
        { data: 'id_status', className: 'td-actions text-center', width:100, orderable : false },
        { data: 'id_question', className: 'td-actions text-center', width:200, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                let status = ''
                if(data == 1) status = 'checked'
                let btn = (MODIFY) ? `<input class="my-switch" type="checkbox" data-toggle="switch"
                        onchange="changeQuestionStatus(${data}, ${row.id_question})" ${status}>
                    <span class="toggle" data-toggle="tooltip" title="Ver más" ></span>`: ''
                return btn
            },
            targets: 2
        },
        {
            render: (data, type, row) => {

                let index = questions.data.indexOf(row)
                if(index == -1 ) questions.data.push(row)

                const showFullQuestion = `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver más" 
                                    onclick="showFullQuestion(${data})">
                                    <i class="fa fa-eye fa-lg"></i>
                                </button>`;
                const btnEdit = (MODIFY) ? `<button class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    onclick="openQuestionModel(${data})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </button>` : '';
                const btnBasisSelection = (MODIFY) ? `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Selección de fundamentos legales" 
                                    onclick="selectBasis(${row.id_question})">
                                    <i class="fa fa-list  fa-lg"></i>
                                </button>` : '';
                const btnShowBasis = `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Visualización de fundamentos legales asignados" 
                                    onclick="showSelectedBasis(${row.id_question})">
                                    <i class="fa fa-list-alt fa-lg"></i>
                                </button>`;
                const btnDelete = (DELETE) ? `<button class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    onclick="deleteQuestion(${data})">
                                    <i class="fa fa-times fa-lg"></i>
                                </button>` : '';
                const btnAnswers = (MODIFY) ? `<button class="btn btn-success btn-link btn-xs" data-toggle="tooltip" title="Respuestas" 
                                    onclick="openAnswers(${data})">
                                    <i class="fa fa-check-square-o fa-lg"></i>
                                </button>` : '';
                let btnDependency = (MODIFY && DELETE) ? `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Bloqueo de preguntas por depedencia" 
                                    onclick="openDependency(${data})">
                                    <i class="fa fa-unlock-alt fa-lg"></i>
                                </button>` : '';
                return showFullQuestion+btnDependency+btnAnswers+btnBasisSelection+btnShowBasis+btnEdit+btnDelete;
            },
            targets: 3
        }
    ],
    rowCallback: function ( row, data ) {
        $('input.my-switch', row).attr({
            'data-on-color':'success', 
            'data-off-color':'dark', 
            'data-on-text':'<i class="fa fa-check"></i>', 
            'data-off-text':'<i class="fa fa-times"></i>'
        }).bootstrapSwitch();
    },
    drawCallback: (settings) => {
        initTooltip();
    }
});
/**
 * Reload questions Datatables
 */
const reloadQuestions = () => { 
    questions.data = []; 
    questionTable.ajax.reload()
}
const reloadQuestionsKeepPage = () => { 
    questions.data = []; 
    questionTable.ajax.reload(null, false)
}
/**
 * Open modal add form
 */
function openQuestionModel (idQuestion = null) {
    $('.loading').removeClass('d-none')
    $('#helpQuestion').summernote('reset');
    document.querySelector('#questionForm').reset();
    $('#questionForm').validate().resetForm();
    $('#questionForm').find(".error").removeClass("error");
    setFields(1, '#IdState', '#IdCity');
    if(idQuestion) {
        $('#titleModal').html('Editar pregunta')
        $('#questionForm').attr('action', '/catalogs/questions/update')
        $('#btnAddQuestion').html('Actualizar')
        $.get(`/catalogs/questions/get/${idQuestion}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        },
        data => {
            document.querySelector('#question').setAttribute('idQuestion', data[0].id_question)
            document.querySelector('#question').value = data[0].question
            document.querySelector('#order').value = data[0].order
            $('#helpQuestion').summernote('code', data[0].help_question)
            // document.querySelector('#IdMatter').value = data[0].id_matter
            // document.querySelector('#IdAspect').value = data[0].id_aspect
            document.querySelector('#IdQuestionType').value = data[0].id_question_type
            document.querySelector('#allow_multiple').value = data[0].allow_multiple_answers
            setFields(data[0].id_question_type, '#IdState', '#IdCity')
            document.querySelector('#IdState').value = data[0].id_state

            setCities(data[0].id_state, '#IdCity', null).then(() => {
                document.querySelector('#IdCity').value = data[0].id_city
                $('.loading').addClass('d-none')
            })
        }).fail(e => {
            $('.loading').addClass('d-none')
            toastAlert(e.statusText, 'error');
        })
    }
    else {
        $('.loading').addClass('d-none')
        $('#titleModal').html('Agregar pregunta')
        $('#questionForm').attr('action', '/catalogs/questions/set')
        $('#btnAddQuestion').html('Registrar')
    }
    $('#addModal').modal({backdrop:'static', keyboard: false});
}

function redirectToFormList(){
    window.location.href = '/v2/catalogs/forms/view'
}

/**
 * set fields by question type in form
 */
function setFields(idQuestionType, selectorIdState, selectorIdCity){
    $('.stateField').addClass('d-none')
    $(selectorIdState).val('')
    $(selectorIdState).attr('data-rule-required', false)
    $(selectorIdState).attr('data-msg-required', '')
    $('.cityField').addClass('d-none')
    $(selectorIdCity).val('')
    $(selectorIdCity).attr('data-rule-required', false)
    $(selectorIdCity).attr('data-msg-required', '')
    const setFilter = filedsByQuestionType[idQuestionType](selectorIdState, selectorIdCity)
}
/**
 * Options fileds by question type
 */
const filedsByQuestionType = {
    '1': (selectorIdState, selectorIdCity) => {
        // Federal
    },
    '2': (selectorIdState, selectorIdCity) => {
        // State
        $('.stateField').removeClass('d-none')
        $(selectorIdState).attr('data-rule-required', true)
        $(selectorIdState).attr('data-msg-required', 'Este campo es obligatorio')
    },
    '3': (selectorIdState, selectorIdCity) => {
        // Unlock
    },
    '4': (selectorIdState, selectorIdCity) => {
        // Local
        $('.stateField').removeClass('d-none')
        $(selectorIdState).attr('data-rule-required', true)
        $(selectorIdState).attr('data-msg-required', 'Este campo es obligatorio')
        $('.cityField').removeClass('d-none')
        $(selectorIdCity).attr('data-rule-required', true)
        $(selectorIdCity).attr('data-msg-required', 'Este campo es obligatorio')
    }
}
/**
 * Handler to submit set Question form 
 */
$('#questionForm').submit( (event) => {
    event.preventDefault();
    const richText = document.querySelector('#helpQuestion').value;
    if (richText == '') {
        toastAlert(`El campo 'Ayuda' no puede estar vacio`, 'warning');
        return;
    }
    if($('#questionForm').valid()) {
        //handler notificaction
        showLoading('#addModal')
        const action = $('#questionForm').attr('action')
        $.post(action, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idQuestion: document.querySelector('#question').getAttribute('idQuestion'),
            question: document.querySelector('#question').value,
            helpQuestion: document.querySelector('#helpQuestion').value,
            order: document.querySelector('#order').value,
            IdMatter: '{{ $parameters->matter_id }}', // remover al quitar columnas
            IdAspect: '{{ $parameters->aspect_id }}', // remover al quitar columnas
            IdQuestionType: document.querySelector('#IdQuestionType').value,
            IdState: document.querySelector('#IdState').value,
            IdCity: document.querySelector('#IdCity').value,
            IdForm: '{{ $parameters->id }}',
            allowMultipleAnswers: document.querySelector('#allow_multiple').value
        },
        data => {
            toastAlert(data.msg, data.status);
            showLoading('#addModal')
            if(data.status == 'success'){
                reloadQuestionsKeepPage();
                $('#addModal').modal('hide');
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            showLoading('#addModal')
        })
    }
});
/**
 * Delete question
 */
function deleteQuestion(idElement)
{
    Swal.fire({
        title: `¿Estas seguro de eliminar esta pregunta?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            $('.loading').removeClass('d-none')
            // send to server
            $.post('/catalogs/questions/delete', {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idQuestion: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadQuestionsKeepPage();
                }
            })
            .fail((e)=>{
                toastAlert(e.statusText, 'error');
                $('.loading').addClass('d-none')
            })
        }
    });
}
/**
 * Show full question info
 */
function showFullQuestion(idQuestion) {
    $.get(`/catalogs/questions/data/view`, {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        idQuestion: idQuestion
    },
    data => {
        if (data.length == 0) {
            toastAlert('Por algun motivo no se encuntra información de la pregunta', 'error');
            return;
        }
        const ques = data[0];
        document.querySelector('#matter-qhtml').innerHTML = ques.matter || 'N/A';
        document.querySelector('#aspect-qhtml').innerHTML = ques.aspect || 'N/A';
        document.querySelector('#order-qhtml').innerHTML = ques.order || 'N/A';
        document.querySelector('#allow_multiple-qhtml').innerHTML = (ques.allow_multiple_answers == 1) ? 'Si, permitir':'No, permitir';
        document.querySelector('#questionType-qhtml').innerHTML = ques.question_type || 'N/A';
        if (ques.state) {
            $('.stateQuestion').removeClass('d-none');
            document.querySelector('#state-qhtml').innerHTML = ques.state || 'N/A';
        } $('.stateQuestion').addClass('d-none');
        if (ques.state) {
            $('.cityQuestion').removeClass('d-none');
            document.querySelector('#city-qhtml').innerHTML = ques.city || 'N/A';
        } $('.cityQuestion').addClass('d-none');
        document.querySelector('#question-qhtml').innerHTML = ques.question || 'N/A';
        document.querySelector('#helpQuestion-qhtml').innerHTML = ques.help_question || 'N/A';
        $('#fullModal').modal({backdrop:'static', keyboard: false});
    })
    .fail(e => {
        toastAlert(e.statusText, 'error');
    })
}
/**
* active/inactive question
 */
function changeQuestionStatus(status, idQuestion)
{// send to server
    $('.loading').removeClass('d-none')
    $.post('/catalogs/questions/change-status',
        {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idQuestion: idQuestion,
            status: status
        },
        (data) => {
            toastAlert(data.msg, data.status);
            reloadQuestionsKeepPage();
            $('.loading').addClass('d-none')
        }
    )
    .fail((e)=>{
        toastAlert(e.statusText, 'error');
        $('.loading').addClass('d-none')
    })
    
}

/**
 * Set aspects for element selection
 */
function setAspects(idMatter, element, inputModel, reload)
{
    document.querySelector(element).value = ''
    return new Promise((resolve, reject) => {
        $.post('/catalogs/matters-aspects', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idMatter: idMatter
        },
        (data) => {
            let options = '<option value="">'+inputModel+'</option>'
            data.forEach((info, index)=>{
                    options += '<option value="'+info.id_aspect+'">'+info.aspect+'</option>'
            });
            document.querySelector(element).innerHTML = options
            if(options != '') resolve(true)
            else reject(false)
            if(reload) reloadQuestionsKeepPage()
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            $('.loading').addClass('d-none')
            reject(false)
        })
    });
}
/**
 * put title current question
 */
function currentQuestion(idQuestion, selector) {
    let current = questions.data.filter( e => e.id_question == idQuestion );
    document.querySelector(selector).innerHTML = current[0].question;
    document.querySelector(selector).setAttribute('title', current[0].question);
    requirements.IdQuestion = idQuestion;
    answers.idQuestion = idQuestion;
    basis.idQuestion = idQuestion;
    initTooltip();
}
</script>