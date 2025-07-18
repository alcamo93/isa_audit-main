<script>

/**
 * Requirements datatable
 */
const subrequirements = {
    idRequirement:null,
    data : [],
    current : []
}
/**
* Open subrequirements table
*/
function showSubrequirements(idRequirement){
    subrequirements.idRequirement = idRequirement
    basis.idRequirement = idRequirement
    let index = requirements.data.findIndex( o => o.id_requirement === idRequirement )
    selection.aplicationType = requirements.data[index].id_application_type
    selection.state = requirements.data[index].id_state
    selection.city = requirements.data[index].id_city
    selection.matter = requirements.data[index].id_matter
    selection.aspect = requirements.data[index].id_aspect
    selection.idRequirementType = requirements.data[index].id_requirement_type
    $('.requirements').addClass('d-none')
    $('.loading').removeClass('d-none')
    setSubrequirimentTypeList()
    .then(() => {
        currentPageReq(idRequirement, '#currentReq');
        reloadSubrequirements()
        $('.loading').addClass('d-none')
        $('.subrequirements').removeClass('d-none')
    })
    .catch(() => {
        toastAlert('No se puede obtener información para subrequerimientos', 'error');
        $('.loading').addClass('d-none')
        $('.requirements').removeClass('d-none')
    })
}

function closeSubRequirements (){
    $('.subrequirements').addClass('d-none')
    $('.loading').removeClass('d-none')
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.requirements').removeClass('d-none')

        subrequirements.idRequirement = null
        selection.state = null
        selection.city = null
        selection.matter = null
        selection.aspect = null
        selection.aplicationType = null

        document.querySelector('#filterSubrequirementNumber').value = ''
        document.querySelector('#filterSubrequirement').value = ''
        document.querySelector('#filterIdEvidence-sr').value = ''
        document.querySelector('#filterIdObtainingPeriod-sr').value = ''
        document.querySelector('#filterIdUpdatePeriod-sr').value = ''
        document.querySelector('#filterIdCondition-sr').value = ''
        document.querySelector('#filterIdRequirementType-sr').value = ''
        
    }, 1000);
    
}

const subrequirementsTable = $('#subrequirementsTable').DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    order: [[ 0, "asc" ]],
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
            data.IdCondition = document.querySelector('#filterIdCondition-sr').value,
            data.IdRequirementType = document.querySelector('#filterIdRequirementType-sr').value,
            data.IdMatter = selection.matter,
            data.IdAspect = selection.aspect,
            data.IdApplicationType = selection.aplicationType,
            data.IdState = selection.state,
            data.IdCity = selection.city,
            data.filterName = null,
            data.algo = 'adios'
        }
    },
    columns: [
        { data: 'order', className: 'td-actions text-center', orderable : true },
        { data: 'no_subrequirement', orderable : true },
        { data: 'subrequirement', orderable : true },
        { data: 'id_subrequirement', className: 'td-actions text-center', width:180, orderable : false }
    ],
    columnDefs: [
        {
            render: (data, type, row) => {

                let index = subrequirements.data.indexOf(row)
                if(index == -1 ) subrequirements.data.push(row)

                let showFullSubRequirement = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Ver más" 
                                    onclick="showDataFullSubrequirement(${data})">
                                    <i class="fa fa-eye fa-lg"></i>
                                </a>`;
                let btnRecomendations = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Recomendaciones" 
                                    onclick="openRecomendations(${data}, 2 )">
                                    <i class="fa fa-sticky-note fa-lg"></i>
                                </a>`;
                let btnSelectBasis = (MODIFY) ? `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Selección de fundamentos" 
                                    onclick="selectBasis(${data}, 'subrequirements', ${selection.aplicationType}, ${selection.state}, ${selection.city})">
                                    <i class="fa fa-list fa-lg"></i>
                                </a>`: '';
                let btnSelectedBasis = `<a class="btn btn-info btn-link btn-xs" data-toggle="tooltip" title="Visualización de fundamentos asignados" 
                                    onclick="showSelectedBasis(${data}, 'subrequirements', 'showBasisSr', ${selection.aplicationType}, ${selection.state},${selection.city})">
                                    <i class="fa fa-list-alt fa-lg"></i>
                                </a>`;
                let btnEdit = (MODIFY) ? `<a class="btn btn-warning btn-link btn-xs" data-toggle="tooltip" title="Editar" 
                                    onclick="openSubrequirimentModel(${data})">
                                    <i class="fa fa-edit fa-lg"></i>
                                </a>` : '';
                let btnDelete = (DELETE) ? `<a class="btn btn-danger btn-link btn-xs" data-toggle="tooltip" title="Eliminar" 
                                    onclick="deleteSubrequirement(${data})">
                                    <i class="fa fa-times fa-lg"></i>
                                </a>` : '';
                return showFullSubRequirement+btnRecomendations+btnSelectBasis+btnSelectedBasis+btnEdit+btnDelete;
            },
            targets: 3
        }
    ],
    drawCallback: (settings) => {
        // Note: added a ajaxComplete to automatically restart tooltip when ajax is finished, is in component_js
        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        })
    }
});
/**
 * Reload Requirements Datatables
 */
const reloadSubrequirements = () => { 
    subrequirements.data = null
    subrequirements.data = []
    subrequirementsTable.ajax.reload() 
}
const reloadSubrequirementsKeepPage = () => { 
    subrequirements.data = null
    subrequirements.data = []
    subrequirementsTable.ajax.reload(null, false) 
}
/**
 * Open modal add form
 */
function openSubrequirimentModel(idSubrequirement = null, disabled = false) {
    $('#subrequirementHelp').summernote('reset');
    $('#subrequirementAcceptance').summernote('reset');
    document.querySelector('#subrequirementForm').reset();
    $('#subrequirementForm').validate().resetForm();
    $('#subrequirementForm').find(".error").removeClass("error");
    if(idSubrequirement){
        getSubequirement(idSubrequirement)
        .then(data => {
            $('.loading').removeClass('d-none')
            // let index = subrequirements.data.findIndex( o => o.id_subrequirement === idSubrequirement )
            $('#subrequirementForm').attr('action', '/catalogs/requirements/subrequirements/update')
            document.querySelector('#subrequirementModalTitle').innerHTML = 'Editar '+data.no_subrequirement;
            document.querySelector('#btnSubmitSubrequirement').innerHTML = 'Editar';
            $('#noSubrequirement').attr('idSubrequirement', data.id_subrequirement);
            document.querySelector('#noSubrequirement').value = data.no_subrequirement;
            document.querySelector('#subrequirement').value = data.subrequirement;
            document.querySelector('#subrequirementDesc').value = data.description;
            $('#subrequirementHelp').summernote('code', data.help_subrequirement); 
            $('#subrequirementAcceptance').summernote('code', data.acceptance);
            document.querySelector('#idEvidence-sr').value = data.id_evidence;
            setSpecificDocument(data.id_evidence, '#subDocument');
            document.querySelector('#subDocument').value = data.document;
            document.querySelector('#IdObtainingPeriod-sr').value = data.id_obtaining_period;
            document.querySelector('#orderSub').value =data.order;
            if(data.id_update_period) document.querySelector('#IdUpdatePeriod-sr').value = data.id_update_period;
            document.querySelector('#IdCondition-sr').value = data.id_condition;
            document.querySelector('#IdRequirementType-sr').value = data.id_requirement_type;
            $('#subrequirementModal').modal({backdrop:'static', keyboard: false});
            $('.loading').addClass('d-none')
        })
        .catch(()=>{
            toastAlert('No se puede obtener información', 'error');
            $('.loading').addClass('d-none')
        });
    }
    else{
        document.querySelector('#btnSubmitSubrequirement').innerHTML = 'Registrar';
        document.querySelector('#subrequirementModalTitle').innerHTML = 'Agregar Subrequerimientos';
        $('#subrequirementForm').attr('action', '/catalogs/requirements/subrequirements/set')
        $('#subrequirementModal').modal({backdrop:'static', keyboard: false});
    }
}
/**
 * Get data subrequiremnts
 */
function getSubequirement(idSubrequirement){
    return new Promise((resolve, reject) => {
        $.post(`/catalogs/requirements/subrequirements/get`, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idSubrequirement: idSubrequirement

        },
        (data) => {
            if (data.length > 0) {
                subrequirements.current = data[0];
                resolve(data[0]);
            }else{
                reject(false);
            }
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
            reject(false);
        })   
    });
}
/**
 * Handler to submit set Requirement form 
 */
$('#subrequirementForm').submit( (event) => {
    event.preventDefault();
    if($('#subrequirementForm').valid()) {
        showLoading('#subrequirementModal')
        //handler notificaction
        action = document.querySelector('#subrequirementForm').getAttribute('action');
        $.post(action, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idSubrequirement: document.querySelector('#noSubrequirement').getAttribute('idSubrequirement'),
            noSubrequirement: document.querySelector('#noSubrequirement').value,
            subrequirement: document.querySelector('#subrequirement').value,
            subrequirementDesc: document.querySelector('#subrequirementDesc').value,
            subrequirementHelp: $('#subrequirementHelp').val(),
            subrequirementAcceptance: $('#subrequirementAcceptance').val(),
            IdEvidence: document.querySelector('#idEvidence-sr').value,
            document: document.querySelector('#subDocument').value,
            IdObtainingPeriod: document.querySelector('#IdObtainingPeriod-sr').value,
            IdUpdatePeriod: document.querySelector('#IdUpdatePeriod-sr').value,
            IdCondition: document.querySelector('#IdCondition-sr').value,
            IdRequirementType: document.querySelector('#IdRequirementType-sr').value,
            idRequirement: subrequirements.idRequirement, 
            order: document.querySelector('#orderSub').value
        },
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#subrequirementModal')
            if(data.status == 'success'){
                reloadSubrequirementsKeepPage();
                $('#subrequirementModal').modal('hide');
            }
        })
        .fail((data)=>{
            toastAlert(data.statusText, 'error');
            showLoading('#subrequirementModal')
        })
    }
});

/**
 * Delete question
 */
function deleteSubrequirement(idElement) {
    let index = subrequirements.data.findIndex( o => o.id_subrequirement === idElement )
    Swal.fire({
        title: '¿Estas seguro de eliminar el requerimiento '+subrequirements.data[index].no_subrequirement+'?',
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
            $.post('/catalogs/requirements/subrequirements/delete',
            {
                _token:document.querySelector('meta[name="csrf-token"]').content,
                idSubrequirement: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadSubrequirementsKeepPage();
                }
            })
            .fail((e)=>{
                $('.loading').addClass('d-none')
                toastAlert(e.statusText, 'error');
            })
        }
    });
}
/**
 * Set aspects for element selection
 */
function setSubrequirimentTypeList() {
     return new Promise((resolve, reject) => {
        $.post('/catalogs/requirements/subrequirement-types', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            applicationType: selection.aplicationType,
            idRequirementType: selection.idRequirementType
        },
        (data) => {
            let filterOptions = '<option value="">Todos</option>'
            let modalOptions = '<option value=""></option>'
            if (data.length > 0) {
                data.forEach(e => {
                    filterOptions += '<option value="'+e.id_requirement_type+'">'+e.requirement_type+'</option>'
                    modalOptions += '<option value="'+e.id_requirement_type+'">'+e.requirement_type+'</option>'
                });
            }
            else {
                filterOptions += '<option value="">No existen subrequerimientos para este tipo</option>'
                modalOptions += '<option value="">No existen subrequerimientos para este tipo</option>'
            }
            document.querySelector('#filterIdRequirementType-sr').innerHTML = filterOptions
            document.querySelector('#IdRequirementType-sr').innerHTML = modalOptions
            resolve(true)
        })
        .fail(e => {
            reject(false)
        });
    });
}

/**
* See full view of requirement data
*/
function showFullSubrequirement(idSubrequirement) {
    getSubequirement(idSubrequirement)
    .then(data => {
        $('.loading').removeClass('d-none')
        // let index = subrequirements.data.findIndex( o => o.id_subrequirement === idSubrequirement )
        $('#requirementViewModalTitle').html('Subrequerimiento ('+data.no_subrequirement+')')
        setAspects(data.id_matter, '#IdAspect_fv', '', false)
        .then(()=>{
            $('#requirement_fv').html(data.subrequirement)
            $('#requirementDesc_fv').html(data.description)
            $('#requirementHelp_fv').html(data.help_subrequirement)
            $('#requirementAcceptance_fv').html(data.acceptance)
            $('#requirementRecommendation_fv').html(data.recommendation)
            /* Matter */
            $('#IdMatter_fv').val(data.id_matter)
            $('#IdMatter_fvs').html($("#IdMatter_fv option:selected").html())
            /* Aspect */
            $('#IdAspect_fv').val(data.id_aspect)
            $('#IdAspect_fvs').html($("#IdAspect_fv option:selected").html())
            /* Audit type */
            $('#IdEvidence_fv').val(data.id_audit_type)
            $('#IdEvidence_fvs').html($("#IdEvidence_fv option:selected").html())
            /* Obtaining period */
            $('#IdObtainingPeriod_fv').val(data.id_obtaining_period)
            $('#IdObtainingPeriod_fvs').html($("#IdObtainingPeriod_fv option:selected").html())
            /* Update period */
            $('#IdUpdatePeriod_fv').val(data.id_update_period)
            $('#IdUpdatePeriod_fvs').html($("#IdUpdatePeriod_fv option:selected").html())
            /* Aplication type */
            $('#IdAplicationType_fv').val(data.id_application_type)
            $('#IdAplicationType_fvs').html($("#IdAplicationType_fv option:selected").html())
            /* Requirement type */
            $('#IdRequirementType_fv').val(data.id_requirement_type)
            $('#IdRequirementType_fvs').html($("#IdRequirementType_fv option:selected").html())
            /* Requirement state */
            if(data.id_application_type == 2){
                $('.state-fv').removeClass('d-none')
                $('#IdState_fv').val(data.id_state)
                $('#IdState_fvs').html($("#IdState_fv option:selected").html())
            }
            else $('.state-fv').addClass('d-none')
            /* condition */
            $('#IdCondition_fv').val(data.id_condition)
            $('#IdCondition_fvs').html($("#IdCondition_fv option:selected").html())
            $('#fullRequirementsViewModal').modal({backdrop:'static', keyboard: false})
            $('.loading').addClass('d-none')
        });
    })
    .catch(()=>{
        toastAlert('No se puede obtener información', 'error');
        $('.loading').addClass('d-none')
    });
}

</script>