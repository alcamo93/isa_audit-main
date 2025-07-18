<script>
/********************************************************************************************************/
/*******************************    Requirement selection     *******************************************/
/********************************************************************************************************/
const requirements = {
    data : [],
    IdQuestion: null
}
const reqCurrentPage = {
    data: []
}
const requirementsTable = $('#requirementsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/questions/answers/requirements/select',
        type: 'POST', 
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            // relation
            data.IdForm = '{{ $parameters->id }}',
            data.idQuestion = requirements.IdQuestion,
            data.idAnswerQuestion = answers.idAnswerQuestion,
            // requirements
            data.filterRequirementNumber = document.querySelector('#filterRequirementNumber').value,
            data.filterRequirement = document.querySelector('#filterRequirement').value,
            data.IdEvidence = document.querySelector('#filterIdEvidence-r').value,
            data.IdObtainingPeriod = document.querySelector('#filterIdObtainingPeriod-r').value,
            data.IdUpdatePeriod = document.querySelector('#filterIdUpdatePeriod-r').value,
            data.IdMatter = selection.matter,
            data.IdAspect = selection.aspect,
            data.IdCondition = null,
            data.IdState = selection.idState,
            data.IdCity = selection.idCity,
            // Note: key "questionType" really is the value that refers to id_requirement_type
            data.IdRequirementType = selection.idRequirementType,
            data.IdAplicationType = selection.idApplicationType,
            // only for special requiremnets
            data.filterIdCustomer = document.querySelector('#filterIdCustomer').value,
            data.filterIdCorporate = document.querySelector('#filterIdCorporate').value
        }
    },
    columns: [
        { data: 'no_requirement', orderable : true },
        { data: 'requirement', orderable : true },
        { data: 'id_requirement', className: 'td-actions text-center', width:150, orderable : false },
        { data: 'id_requirement', className: 'td-actions text-center', width:150, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {

                return (row.has_subrequirement) ? `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver subrequerimientos" 
                    onclick="showSubrequirement(${data})">
                    <i class="fa fa-list fa-lg"></i>
                    </button>` : '';
            },
            targets: 2
        },
        {
            render: (data, type, row) => {
                reqCurrentPage.data.push(row);

                let color = 'danger';
                let icon = 'times';
                let status = false
                let tooltip = 'Clic para asignar requerimiento';

                if (row.relation != null) {
                    tooltip = 'Clic para remover requerimiento';
                    color = 'success';
                    icon = 'check';
                    status = true
                }

                let btnChangeRequirement = `<button class="btn btn-${color} btn-link btn-xs" data-toggle="tooltip" title="${tooltip}"
                            onclick="setQuestionRequirement(${data}, null ,${status}, ${row.has_subrequirement})">
                            <i class="fa fa-${icon} fa-lg"></i>
                        </button>`;
                return btnChangeRequirement
            },
            targets: 3
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        initTooltip();
    }
});

const selectedRequirementsTable = $('#selectedRequirementsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/questions/answers/requirements/assigned',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.IdQuestion = requirements.IdQuestion,
            data.idAnswerQuestion = answers.idAnswerQuestion,
            // data.idRequirement = 
            data.filterRequirementNumber = document.querySelector('#filterRequirementNumber-rs').value,
            data.filterRequirement = document.querySelector('#filterRequirement-rs').value,
            data.IdEvidence = document.querySelector('#filterIdEvidence-rs').value,
            data.IdObtainingPeriod = document.querySelector('#filterIdObtainingPeriod-rs').value
            data.IdUpdatePeriod = document.querySelector('#filterIdUpdatePeriod-rs').value
            data.IdMatter = selection.matter,
            data.IdAspect = selection.aspect,
            data.IdRequirementType = selection.idRequirementType,
            data.IdAplicationType = selection.idApplicationType
        }
    },
    columns: [
        { data: 'no_requirement', orderable : true },
        { data: 'requirement', orderable : true },
        { data: 'id_requirement', className: 'td-actions text-center', width:150, orderable : false },
        { data: 'id_requirement', className: 'td-actions text-center', width:150, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                return (row.has_subrequirement) ? `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver subrequerimientos" 
                        onclick="showSelectedSubrequirement(${data})">
                        <i class="fa fa-list-alt fa-lg"></i>
                    </button>` : '';
            },
            targets: 2
        },
        {
            render: (data, type, row) => {
                reqCurrentPage.data.push(row)
                let index = requirements.data.indexOf(row)
                if(index == -1 ) requirements.data.push(row)
                return `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver más" 
                    onclick="showDataRequirement(${data})">
                    <i class="fa fa-eye fa-lg"></i>
                    </button>`
            },
            targets: 3
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        initTooltip();
    }
});

/**
 * Reload requirements Datatables
 */
const reloadRequirements = () => {
    getQuestionRequirements()
    requirementsTable.ajax.reload()
}
const reloadRequirementsKeepPage = () => {
    getQuestionRequirements()
    requirementsTable.ajax.reload(null, false)
}
/**
 * Reload requierements Datatables
 */
const reloadSelectedRequirements = () => {
    selectedRequirementsTable.ajax.reload()
}
const reloadSelectedRequirementsKeepPage = () => {
    selectedRequirementsTable.ajax.reload(null, false)
}
/**
 * Open Requirements selection view
 */
function selectRequeriments(idQuestion, idAnswerQuestion) {
    currentQuestion(idQuestion, '#currentQuestion-p2')
    currentAnswer(idAnswerQuestion, '#currentAnswer-p2')
    selectQuestion(idQuestion)
    filterCustomer(selection.idRequirementType, '#areaFilterCustomer', '#filterIdCustomer', '#filterIdCorporate');
    $('.showAnswers').addClass('d-none')
    $('.loading').removeClass('d-none')
    getQuestionRequirements()
    .then(()=>{
        requirements.data = []
        reloadRequirements()    
        $('.requirementsSelection').removeClass('d-none')
        $('.loading').addClass('d-none')
    })    
}
/**
* close requirents section 
*/
function closeRequirementSelection () {
    $('.requirementsSelection').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.showAnswers').removeClass('d-none')
        requirements.IdQuestion = null
        clearQuestionSelection()
        requirements.data = []
        reqCurrentPage.data = []
        document.querySelector('#filterRequirementNumber').value = ''
        document.querySelector('#filterRequirement').value = ''
        document.querySelector('#filterIdEvidence-r').value = ''
        document.querySelector('#filterIdObtainingPeriod-r').value = ''
        document.querySelector('#filterIdUpdatePeriod-r').value = ''
    }, 1000);
}
/**
* Open Requirements selection view
*/
function getQuestionRequirements(){
    $('.loading').removeClass('d-none')
    return new Promise ((resolve, reject) => {
        $.post(`/catalogs/questions/answers/requirements/${requirements.IdQuestion}/${answers.idAnswerQuestion}`, { 
            _token: document.querySelector('meta[name="csrf-token"]').content 
        },
        (data)=>{
            if(data){
                requirements.data = data
                $('.loading').addClass('d-none')
                resolve(true)
            }
            else {
                $('.loading').addClass('d-none')
                reject(false)
            }
        })
        .fail((e)=>{
            $('.loading').addClass('d-none')
            toastAlert(e.statusText, 'error');
            reject(false)
        });
    })
}
/**
 * Alert in set relation with subrequirements
 */
function setQuestionRequirement(idRequirement, idSubrequirement = null, status, hasSubrequirement){
    const actionTitle = (status == 0) ? 'Agregar' : 'Retirar';
    const actionDescription = (status == 0) ? 'seleccionas uno o varios' : 'retiras todos los';
    if (hasSubrequirement == 1) {
        Swal.fire({
            title: `¿Quieres ${actionTitle} todos los subrequerimientos que contiene?`,
            text: `Si ${actionDescription} subrequirimientos se ${actionTitle}á tambien este requerimiento`,
            icon: 'warning',
            allowOutsideClick: false,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Si, ${actionTitle}!`,
            cancelButtonText: 'No, cancelar!'
        }).then((result) => {
            if (result.value) {
                $('.loading').removeClass('d-none')
                setQuestionRequirementService(idRequirement, idSubrequirement, status, hasSubrequirement)
                .then(data => {
                    $('.loading').addClass('d-none')
                })
                .catch(e => {
                    $('.loading').addClass('d-none');
                    toastAlert(e, 'error');
                });
            }
        });
    }
    else {
        $('.loading').removeClass('d-none')
        setQuestionRequirementService(idRequirement, idSubrequirement, status, hasSubrequirement)
        .then(data => {
            $('.loading').addClass('d-none')
        })
        .catch(e => {
            $('.loading').addClass('d-none');
            toastAlert(e, 'error');
        });
    }
}
/**
* Sets or destroys the relation between questions and requierements
*/
function setQuestionRequirementService(idRequirement, idSubrequirement = null, status, hasSubrequirement) {
    return new Promise ((resolve, reject) => {
        $.post('/catalogs/questions/answers/requirements/relation', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idQuestion: requirements.IdQuestion,
            idAnswerQuestion: answers.idAnswerQuestion,
            idRequirement: idRequirement,
            idSubrequirement: idSubrequirement,
            status: !status,
            hasSubrequirement: hasSubrequirement
        },
        (data) => {
            toastAlert(data.msg, data.status);
            if(data.status == 'success'){ 
                reloadRequirementsKeepPage()
                reloadSubrequirementsKeepPage()
            }
            resolve(true)
        })
        .fail((e)=>{
            reject(e.statusText)
        });
    });
}
/**
* Show selected requirements
*/
function showSelectedRequeriments(idQuestion, idAnswerQuestion) {
    selectQuestion(idQuestion)
    currentQuestion(idQuestion, '#currentQuestion-p1')
    currentAnswer(idAnswerQuestion, '#currentAnswer-p1')
    $('.showAnswers').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.showSelectedRequirements').removeClass('d-none')
        reloadSelectedRequirements()
    }, 1000);
}
/**
* Close selected requirements
*/
function closeSelectedRequeriments ()
{
    requirements.IdQuestion = null
    clearQuestionSelection()
    $('.showSelectedRequirements').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.showAnswers').removeClass('d-none')
        requirements.data = []
        reqCurrentPage.data = []
        document.querySelector('#filterRequirementNumber-rs').value = ''
        document.querySelector('#filterRequirement-rs').value = ''
        document.querySelector('#filterIdEvidence-rs').value = ''
        document.querySelector('#filterIdObtainingPeriod-rs').value = ''
        document.querySelector('#filterIdUpdatePeriod-rs').value = ''
    }, 1000);
}
/**
* See full view of requirement data
*/
function showFullRequirement(idRequirement)
{
    let index = requirements.data.findIndex( o => o.id_requirement === idRequirement )
    $('#requirementViewModalTitle').html('Requerimiento '+requirements.data[index].no_requirement)
    setAspects(requirements.data[index].id_matter, '#IdAspect_fv', '', false)
    .then(()=>{
        $('#requirement_fv').html(requirements.data[index].requirement)
        $('#requirementDesc_fv').html(requirements.data[index].description)
        $('#requirementHelp_fv').html(requirements.data[index].help_requirement)
        $('#requirementAcceptance_fv').html(requirements.data[index].acceptance)
        $('#requirementRecommendation_fv').html(requirements.data[index].recommendation)
        /* Matter */
        $('#IdMatter_fv').val(requirements.data[index].id_matter)
        $('#IdMatter_fvs').html($("#IdMatter_fv option:selected").html())
        /* Aspect */
        $('#IdAspect_fv').val(requirements.data[index].id_aspect)
        $('#IdAspect_fvs').html($("#IdAspect_fv option:selected").html())
        /* Audit type */
        $('#IdEvidence_fv').val(requirements.data[index].id_audit_type)
        $('#IdEvidence_fvs').html($("#IdEvidence_fv option:selected").html())
        /* Obtaining period */
        $('#IdObtainingPeriod_fv').val(requirements.data[index].id_obtaining_period)
        $('#IdObtainingPeriod_fvs').html($("#IdObtainingPeriod_fv option:selected").html())
        /* Update period */
        $('#IdUpdatePeriod_fv').val(requirements.data[index].id_update_period)
        $('#IdUpdatePeriod_fvs').html($("#IdUpdatePeriod_fv option:selected").html())
        /* Aplication type */
        $('#IdAplicationType_fv').val(requirements.data[index].id_application_type)
        $('#IdAplicationType_fvs').html($("#IdAplicationType_fv option:selected").html())
        /* Requirement type */
        $('#IdRequirementType_fv').val(requirements.data[index].id_requirement_type)
        $('#IdRequirementType_fvs').html($("#IdRequirementType_fv option:selected").html())
        /* Requirement state */
        if(requirements.data[index].id_application_type == 2){
            $('.state-fv').removeClass('d-none')
            $('#IdState_fv').val(requirements.data[index].id_state)
            $('#IdState_fvs').html($("#IdState_fv option:selected").html())
        }
        else $('.state-fv').addClass('d-none')
        /* condition */
        $('#IdCondition_fv').val(requirements.data[index].id_condition)
        $('#IdCondition_fvs').html($("#IdCondition_fv option:selected").html())
        $('#fullRequirementsViewModal').modal({backdrop:'static', keyboard: false})
    })
}
</script>