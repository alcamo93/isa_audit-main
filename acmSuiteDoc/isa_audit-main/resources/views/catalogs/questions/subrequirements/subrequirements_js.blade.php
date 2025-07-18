<script>

/**************************************************************************************/
/***********************************  Subrequirements *********************************/
/**************************************************************************************/

const subrequirements = {
    idRequirement: null,
    data: []
}

const subrequirementsTable = $('#subrequirementsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/requirements/subrequirements',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.idRequirement = subrequirements.idRequirement,
            data.filterSubrequirementNumber = document.querySelector('#filterSubrequirementNumber').value,
            data.filterSubrequirement = document.querySelector('#filterSubrequirement').value,
            data.IdEvidence = document.querySelector('#filterIdEvidence-sr').value,
            data.IdObtainingPeriod = document.querySelector('#filterIdObtainingPeriod-sr').value,
            data.IdUpdatePeriod = document.querySelector('#filterIdUpdatePeriod-sr').value,
            data.IdMatter = selection.matter,
            data.IdAspect = selection.aspect,
            data.IdRequirementType = selection.subrequirementType,
            data.filterName = null,
            data.IdApplicationType = null,
            data.IdCondition = null,
            data.IdState = null,
            data.IdCity = null
        }
    },
    columns: [
        { data: 'no_subrequirement', orderable : true },
        { data: 'subrequirement', orderable : true },
        { data: 'id_subrequirement', className: 'td-actions text-center', width:150, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {

                let r = requirements.data.filter( o => o.id_requirement === row.id_requirement ) 
                let indexSub = r.findIndex( o => o.id_subrequirement === row.id_subrequirement ) 
                

                let color = 'danger';
                let icon = 'times';
                let status = false
                let tooltip = 'Clic para asignar requerimiento';
                
                if ( indexSub > -1 ) {
                    tooltip = 'Clic para remover requerimiento';
                    color = 'success';
                    icon = 'check';
                    status = true
                }

                let btnChangeRequirement = `<button class="btn btn-${color} btn-link btn-xs" data-toggle="tooltip" title="${tooltip}"
                            onclick="setQuestionRequirement(${row.id_requirement}, ${row.id_subrequirement}, ${status}, null)">
                            <i class="fa fa-${icon} fa-lg"></i>
                        </button>`;
                return btnChangeRequirement
            },
            targets: 2
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        initTooltip();
    }
});

const selectedSubrequirementsTable = $('#selectedSubrequirementsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    dom: `<'row'<'col-sm-12'tr>> <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    language: {
        url: '/assets/lenguage.json'
    },
    ajax: { 
        url: '/catalogs/questions/answers/subrequirements/assigned',
        type: 'POST',
        data:  (data) => {
            data._token = document.querySelector('meta[name="csrf-token"]').content,
            data.IdQuestion = requirements.IdQuestion,
            data.idAnswerQuestion = answers.idAnswerQuestion,
            data.idRequirement = subrequirements.idRequirement,
            data.filterSubrequirementNumber = document.querySelector('#filterSubrequirementNumber-srs').value,
            data.filterSubrequirement = document.querySelector('#filterSubrequirement-srs').value,
            data.IdEvidence = document.querySelector('#filterIdEvidence-srs').value,
            data.IdObtainingPeriod = document.querySelector('#filterIdObtainingPeriod-srs').value,
            data.IdUpdatePeriod = document.querySelector('#filterIdUpdatePeriod-srs').value,
            data.IdMatter = selection.matter,
            data.IdAspect = selection.aspect,
            data.IdRequirementType = selection.subrequirementType
        }
    },
    columns: [
        { data: 'no_subrequirement', orderable : true },
        { data: 'subrequirement', orderable : true },
        { data: 'id_subrequirement', className: 'td-actions text-center', width:150, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {
                let index = subrequirements.data.indexOf(row)
                if(index == -1 ) subrequirements.data.push(row)
                return `<button class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver más" 
                        onclick="showDataFullSubrequirement(${data})">
                        <i class="fa fa-eye fa-lg"></i>
                    </button>`                
            },
            targets: 2
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        initTooltip();
    }
});

reloadSubrequirements = () => {
    getQuestionRequirements()
    subrequirementsTable.ajax.reload()
}
reloadSubrequirementsKeepPage = () => {
    getQuestionRequirements()
    subrequirementsTable.ajax.reload(null, false)
}
reloadSelectedSubrequirements = () => {
    selectedSubrequirementsTable.ajax.reload(null, false)
}
/**
* Show subrequirements for selection list
*/
function showSubrequirement(idRequirement) {
    currenRequiremnet(idRequirement, 'p2')
    $('.requirementsSelection').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.subrequirementsSelection').removeClass('d-none')
        reloadSubrequirements()
    }, 1000);
}
/**
 * current tittle requirement
 */
function currenRequiremnet(idRequirement, selector) {
    // Set current question 
    let currentQuestion = questions.data.filter( e => e.id_question == requirements.IdQuestion );
    document.querySelector('#currentQuestionReq-'+selector).innerHTML = currentQuestion[0].question;
    document.querySelector('#currentQuestionReq-'+selector).setAttribute('title', currentQuestion[0].question);
    // set current answer
    let current = answers.data.filter( e => e.id_answer_question == answers.idAnswerQuestion );
    document.querySelector('#currentAnswerReq-'+selector).innerHTML = current[0].description;
    document.querySelector('#currentAnswerReq-'+selector).setAttribute('title', current[0].description);
    // set current requirement
    let currentReq = reqCurrentPage.data.filter( e => e.id_requirement == idRequirement );
    document.querySelector('#currentReq-'+selector).innerHTML = currentReq[0].no_requirement;
    document.querySelector('#currentReq-'+selector).setAttribute('title', currentReq[0].no_requirement);
    subrequirements.idRequirement = idRequirement;
    initTooltip();
}
/**
* Close subrequirements selection list
*/
function closeSubRequirements ()
{
    subrequirements.idRequirement = null
    $('.subrequirementsSelection').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.requirementsSelection').removeClass('d-none')
        document.querySelector('#filterSubrequirementNumber').value = ''
        document.querySelector('#filterSubrequirement').value = ''
        document.querySelector('#filterIdEvidence-sr').value = ''
        document.querySelector('#filterIdObtainingPeriod-sr').value = ''
        document.querySelector('#filterIdUpdatePeriod-sr').value = ''
    }, 1000);
}
/**
* Show selected subrequirements 
*/
function showSelectedSubrequirement(idRequirement) {
    currenRequiremnet(idRequirement, 'p1');
    $('.showSelectedRequirements').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.subrequirementsSelected').removeClass('d-none')
        reloadSelectedSubrequirements()
    }, 1000);
}
/**
*
*/
function closeSelectedSubRequirements()
{
    subrequirements.idRequirement = null
    $('.subrequirementsSelected').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.showSelectedRequirements').removeClass('d-none')
        document.querySelector('#filterSubrequirementNumber-srs').value = ''
        document.querySelector('#filterSubrequirement-srs').value = ''
        document.querySelector('#filterIdEvidence-srs').value = ''
        document.querySelector('#filterIdObtainingPeriod-srs').value = ''
        document.querySelector('#filterIdUpdatePeriod-srs').value = ''
    }, 1000);
}
/**
* See full view of requirement data
*/
function showFullSubrequirement(idSubrequirement)
{
    let index = subrequirements.data.findIndex( o => o.id_subrequirement === idSubrequirement )
    console.log({index}, subrequirements.data[index])
    $('#requirementViewModalTitle').html('Subrequerimiento ('+subrequirements.data[index].no_subrequirement+')')
    setAspects(subrequirements.data[index].id_matter, '#IdAspect_fv', '', false)
    .then((data)=>{
        console.log(data)
        $('#requirement_fv').html(subrequirements.data[index].subrequirement)
        $('#requirementDesc_fv').html(subrequirements.data[index].description)
        $('#requirementHelp_fv').html(subrequirements.data[index].help_subrequirement)
        $('#requirementAcceptance_fv').html(subrequirements.data[index].acceptance)
        $('#requirementRecommendation_fv').html(subrequirements.data[index].recommendation)
        /* Matter */
        $('#IdMatter_fv').val(subrequirements.data[index].id_matter)
        $('#IdMatter_fvs').html($("#IdMatter_fv option:selected").html())
        /* Aspect */
        $('#IdAspect_fv').val(subrequirements.data[index].id_aspect)
        $('#IdAspect_fvs').html($("#IdAspect_fv option:selected").html())
        /* Audit type */
        $('#IdEvidence_fv').val(subrequirements.data[index].id_audit_type)
        $('#IdEvidence_fvs').html($("#IdEvidence_fv option:selected").html())
        /* Obtaining period */
        $('#IdObtainingPeriod_fv').val(subrequirements.data[index].id_obtaining_period)
        $('#IdObtainingPeriod_fvs').html($("#IdObtainingPeriod_fv option:selected").html())
        /* Update period */
        $('#IdUpdatePeriod_fv').val(subrequirements.data[index].id_update_period)
        $('#IdUpdatePeriod_fvs').html($("#IdUpdatePeriod_fv option:selected").html())
        /* Aplication type */
        $('#IdAplicationType_fv').val(subrequirements.data[index].id_application_type)
        $('#IdAplicationType_fvs').html($("#IdAplicationType_fv option:selected").html())
        /* Requirement type */
        $('#IdRequirementType_fv').val(subrequirements.data[index].id_requirement_type)
        $('#IdRequirementType_fvs').html($("#IdRequirementType_fv option:selected").html())
        /* Requirement state */
        if(subrequirements.data[index].id_application_type == 2){
            $('.state-fv').removeClass('d-none')
            $('#IdState_fv').val(subrequirements.data[index].id_state)
            $('#IdState_fvs').html($("#IdState_fv option:selected").html())
        }
        else $('.state-fv').addClass('d-none')
        /* condition */
        $('#IdCondition_fv').val(subrequirements.data[index].id_condition)
        $('#IdCondition_fvs').html($("#IdCondition_fv option:selected").html())
        $('#fullRequirementsViewModal').modal({backdrop:'static', keyboard: false})
    })
}
</script>