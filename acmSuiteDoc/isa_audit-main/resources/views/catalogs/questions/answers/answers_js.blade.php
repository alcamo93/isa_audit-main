<script>
const answers = {
    current: [],
    idAnswerQuestion: null,
    idQuestion: null,
    data: []
}
/**
 * Get audit aspects by matter
 */
const answerTable = $('#answerTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: "/assets/lenguage.json"
    },
    ajax: { 
        url: '/catalogs/questions/answers/dt',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.filterAnswer = document.querySelector('#filterAnswer').value,
            data.filterIdAnswerValue = document.querySelector('#filterIdAnswerValue').value,
            data.idQuestion = answers.idQuestion
        }
    },
    columns: [
        { data: 'order', className: 'text-center', orderable : true },
        { data: 'description', className: 'text-center', orderable : true },
        { data: 'id_answer_question', className: 'text-center td-actions', orderable : false, visible: true }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                answers.data.push(row);
                let btnEdit = (MODIFY) ? `<button class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    onclick="openModalAnswer(${true}, ${row.id_answer_question})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </button>` : '';
                let btnDelete = (DELETE) ? `<button class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    onclick="deleteAnswer('${row.description}',${row.id_answer_question})">
                                    <i class="fa fa-times fa-lg"></i>
                                </button>` : ''; 
                let btnRequirementsSelection = (MODIFY && row.id_answer_value == 1) ? `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Selección de requerimientos" 
                                    onclick="selectRequeriments(${row.id_question}, ${row.id_answer_question})">
                                    <i class="fa fa-list  fa-lg"></i>
                                </button>` : '';
                let btnShowRequirement = (row.id_answer_value == 1) ? `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Visualización de requerimientos asignados" 
                                    onclick="showSelectedRequeriments(${row.id_question}, ${row.id_answer_question})">
                                    <i class="fa fa-list-alt fa-lg"></i>
                                </button>` : '';
                return btnRequirementsSelection+btnShowRequirement+btnEdit+btnDelete;
            },
            targets: 2
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        initTooltip();
    }
});
/**
 * Reload aspects and execute progressMatter
 */
const reloadAnswers = () => { 
    answerTable.ajax.reload(null, false); 
}
/**
 * Open Requirements selection view
 */
function openAnswers(idQuestion) {
    currentQuestion(idQuestion, '#currentQuestion-answer')
    $('.questions').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        reloadAnswers()    
        $('.showAnswers').removeClass('d-none')
        $('.loading').addClass('d-none')
    }, 1000);   
}
/**
 * put title current answer
 */
function currentAnswer(idAnswerQuestion, selector) {
    answers.idAnswerQuestion = idAnswerQuestion;
    let current = answers.data.filter( e => e.id_answer_question == idAnswerQuestion );
    document.querySelector(selector).innerHTML = current[0].description;
    document.querySelector(selector).setAttribute('title', current[0].description);
    initTooltip();
}
/**
 * Open Requirements selection view
 */
function closeAnswers() {
    answers.idQuestion = null; 
    $('.showAnswers').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        reloadQuestionsKeepPage()    
        $('.questions').removeClass('d-none')
        $('.loading').addClass('d-none')
    }, 1000);   
}
/**
 * Open modal add processes
 */
function openModalAnswer(isUpdate, idAnswerQuestion = null) {
    document.querySelector('#setAnswerForm').reset();
    $('#setAnswerForm').validate().resetForm();
    $('#setAnswerForm').find(".error").removeClass("error");
    if (isUpdate) {
        getDataAnswer(idAnswerQuestion)
        .then( res => {
            document.querySelector('#answer').value = answers.current.description;
            document.querySelector('#orderAnswer').value = answers.current.order || '';
            document.querySelector('#idAnswerValue').value = answers.current.id_answer_value;
            $('#addModalAnswer').modal({backdrop:'static', keyboard: false});
        })
        .catch(()=>{
            toastAlert('No se puede obtener información', 'error');
        });
    }
    else {
        answers.current = [];
        $('#addModalAnswer').modal({backdrop:'static', keyboard: false});
    }
}
/**
 * Get data answer
 */
function getDataAnswer(idAnswer){
    return new Promise ((resolve, reject) => {
        $.post(`/catalogs/questions/answers/${idAnswer}`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        },
        (data)=>{
            answers.current = data[0];
            resolve(true)
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            reject(false)
        });
    });
}
/**
 * Handler to submit set/update answer form 
 */
$('#setAnswerForm').submit( (event) => {
    event.preventDefault();
    if($('#setAnswerForm').valid()) {
        showLoading('#addModalAnswer');
        //handler notificaction
        setAnswerService()
        .then(()=>{
            showLoading('#addModalAnswer');
            $('#addModalAnswer').modal('hide');
        })
        .catch(()=>{
            toastAlert('Error Al registrar respuesta', 'error');
            showLoading('#addModalAnswer');
        });
    }
});
/**
* Sets the question-basis relation
*/
function setAnswerService(){
    return new Promise ((resolve, reject) => {
        $.post('/catalogs/questions/answers', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idQuestion: answers.idQuestion,
            idAnswer: (answers.current.id_answer_question) ? answers.current.id_answer_question : null,
            answer: document.querySelector('#answer').value,
            order: document.querySelector('#orderAnswer').value,
            idAnswerValue: document.querySelector('#idAnswerValue').value
        },
        (data)=>{
            switch (data.status) {
                case 'success':
                    toastAlert(data.msg, data.status);
                    reloadAnswers();
                    resolve(true)
                    break;
                case 'warning':
                    okAlert(data.title, data.msg, data.status);
                    reject(false)
                    break;
                case 'error':
                    toastAlert(data.msg, data.status);
                    reloadAnswers();
                    reject(false)
                    break;
                default:
                    break;
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            reject(false)
        });
    });
}
/**
 * Delete Answer
 */
function deleteAnswer(answer, idElement) {
    Swal.fire({
        title: `¿Estas seguro de eliminar "${answer}"?`,
        text: 'El cambio será permanente al confirmar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!'
    }).then((result) => {
        if (result.value) {
            // send to server
            $.post('/catalogs/questions/answer/delete', {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idAnswerQuestion: idElement
            },
            data => {
                toastAlert(data.msg, data.status);
                reloadAnswers();
            })
            .fail(e => {
                toastAlert(e.statusText, 'error');
            });
        }
    });
}
</script>