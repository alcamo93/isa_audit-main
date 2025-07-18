<script>
$(document).ready( () => {
    setFormValidation('#requirementForm');
    setFormValidation('#subrequirementForm');
    const toolbar = {
        dialogsInBody: true,
        dialogsFade: true,
        lang: 'es-ES',
        placeholder: 'Especifica el formato del texto de este campo',
        tabsize: 2,
        height: 100,
        minHeight: null,
        maxHeight: null, 
        toolbar: [
            ['font', ['bold', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
        ]
    }
    $('#requirementHelp').summernote(toolbar);
    $('#subrequirementHelp').summernote(toolbar);
    $('#requirementAcceptance').summernote(toolbar);
    $('#subrequirementAcceptance').summernote(toolbar);
});

function redirectToFormList(){
    window.location.href = '/v2/catalogs/forms/view'
}
/**
 * Requirements datatable
 */
const requirements = {
    data : [],
    current : []
}

const selection = {
    state : null, 
    matter: null,
    city: null,
    aspect: null,
    // para subrequirements
    aplicationType: null,
    idRequirementType: null
}
/**
 * 
 * IMPORTANT NOTE:
 * The specific "submodule catalogs" and "module specific requirements" share the same script, 
 * the functionalities are different parameters search in "functions_requiremenst_js.blade".
 * 
 */
/**
 * Reload Requirements Datatables
 */
const reloadRequirements = () => { 
    requirements.data = []; 
    requirementsTable.ajax.reload() 
}
const reloadRequirementsKeepPage = () => { 
    requirements.data = []; 
    requirementsTable.ajax.reload(null, false) 
}
/**
 * Open modal add form
 */
function openRequirementModel(idRequirement = null, disabled = false, isNormal = true) {
    document.querySelector('#requirementForm').reset();
    $('#requirementForm').validate().resetForm();
    $('#requirementForm').find(".error").removeClass("error");
    if (isNormal) {
        setSpecificDocument('', '#document');   
        $('#requirementHelp').summernote('reset');
        $('#requirementAcceptance').summernote('reset');
    }
    // set Data
    if(idRequirement){
        $('.loading').removeClass('d-none')
        getRequirement(idRequirement)
        .then(()=>{ 
            $('#requirementForm').attr('action', '/catalogs/requirements/requirements/update')
            $('#requirementModalTitle').html('Editar '+requirements.current.no_requirement)
            $('#btnSubmitRequirement').html('Actualizar')
            // setAspects(requirements.current.id_matter, '#IdAspect', '', false)
            // .then(() => 
            setDataInForm(true)
            .then(() => {
                $('.loading').addClass('d-none')
                $('#requirementModal').modal({backdrop:'static', keyboard: false});
            })
            .catch(e => {
                toastAlert('No se puede mostrar la información', 'error');
                $('.loading').addClass('d-none')
            })
        })
        .catch((error)=>{
            toastAlert('No se puede obtener información', 'error');
            $('.loading').addClass('d-none')
        });
    }
    else{
        const titleModal = isNormal ? 'Agregar Requerimiento' : 'Agregar Requerimiento de Condicionantes, actas u otros';
        $('#btnSubmitRequirement').html('Registrar')
        $('#requirementModalTitle').html(titleModal) 
        $('#requirementForm').attr('action', '/catalogs/requirements/requirements/set')
        $('#requirementModal').modal({backdrop:'static', keyboard: false});
    }
    
}
/**
 * Get data requiremnts
 */
function getRequirement(idRequirement){
    return new Promise((resolve, reject) => {
        $.post(`/catalogs/requirements/requirements/get`, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idRequirement: idRequirement
        },
        (data) => {
            if (data.length > 0) { 
                requirements.current = data[0];
                resolve(true);
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
$('#requirementForm').submit( (event) => {
    event.preventDefault();
    if($('#requirementForm').valid()) {
        // const updateIdRequirementType = document.querySelector('#IdRequirementType').value;
        // if ( (requirements.current.id_requirement_type == 5 || requirements.current.id_requirement_type == 17) &&
        //     (updateIdRequirementType != requirements.current.id_requirement_type) ) {
        //     console.log('true');
        // }
        // else {
        //     console.log('false');
        // }
        // return;
        showLoading('#requirementModal')
        //handler notificaction
        action = document.querySelector('#requirementForm').getAttribute('action');
        $.post(action, getDataInForm(),
        (data) => {
            toastAlert(data.msg, data.status);
            showLoading('#requirementModal')
            if(data.status == 'success'){
                reloadRequirementsKeepPage();
                $('#requirementModal').modal('hide');
            }
        })
        .fail((e)=>{
            showLoading('#requirementModal')
            toastAlert(e.statusText, 'error');
        })
    }
});

/**
 * Delete question
 */
function deleteRequirement(idElement) {
    let index = requirements.data.findIndex( o => o.id_requirement === idElement )
    Swal.fire({
        title: '¿Estas seguro de eliminar el requerimiento '+requirements.data[index].no_requirement+'?',
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
            $('.loading').removeClass('d-none')
            $.post('/catalogs/requirements/requirements/delete', {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                idRequirement: idElement
            },
            (data) => {
                toastAlert(data.msg, data.status);
                $('.loading').addClass('d-none')
                if(data.status == 'success'){
                    reloadRequirementsKeepPage();
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
function setAspects(idMatter, element, inputModel, reload) {
     return new Promise((resolve, reject) => {
        $.post('/catalogs/matters-aspects', {
            _token:  document.querySelector('meta[name="csrf-token"]').content,
            idMatter : idMatter
        },
        (data) => {
            let options = '<option value="">'+inputModel+'</option>'
            data.forEach((info, index)=>{
                    options += '<option value="'+info.id_aspect+'">'+info.aspect+'</option>'
            });
            document.querySelector(element).innerHTML = options
            if(options != '') resolve(true)
            else reject(false)
            if(reload) reloadRequirements()
        })
        .fail((e)=>{
            toastAlert(e.statusText, 'error');
        })
    });
}
/**
 * validations for idAplication Type
 */
const fieldsInForm = {
    '1': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 1;
        setStates(1, selectorIdState, selectorIdCity, origin)
    },
    '2': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 2;
        setStates(2, selectorIdState, selectorIdCity, origin)
    },
    '3': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = '';
        $(selectorIdAplicationType).attr('disabled', false);
    },
    '4': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 2;
        setStates(2, selectorIdState, selectorIdCity, origin)
    },
    '5': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = '';
        $(selectorIdAplicationType).attr('disabled', false);
    },
    '6': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 1;
        setStates(1, selectorIdState, selectorIdCity, origin)
    },
    '7': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 1;
        setStates(1, selectorIdState, selectorIdCity, origin)
    },
    '8': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 2;
        setStates(2, selectorIdState, selectorIdCity, origin)
    },
    '9': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 2;
        setStates(2, selectorIdState, selectorIdCity, origin)
    },
    '10': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 2;
        setStates(2, selectorIdState, selectorIdCity, origin) 
    },
    '11': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = '';
        $(selectorIdAplicationType).attr('disabled', false);
        setStates(11, selectorIdState, selectorIdCity, origin) 
    },
    '12': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 4;
        setStates(4, selectorIdState, selectorIdCity, origin)
    },
    '13': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 4;
        setStates(4, selectorIdState, selectorIdCity, origin)
    },
    '14': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 4;
        setStates(4, selectorIdState, selectorIdCity, origin)
    },
    '15': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 4;
        setStates(4, selectorIdState, selectorIdCity, origin)
    },
    '16': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = 4;
        setStates(4, selectorIdState, selectorIdCity, origin)
    },
    '17': (selectorIdAplicationType, selectorIdState, selectorIdCity, origin) => {
        document.querySelector(selectorIdAplicationType).value = '';
        $(selectorIdAplicationType).attr('disabled', false);
    },
}

function setAplicationType(idRequirementType, selectorAplicationType, selectorIdState, selectorIdCity, origin = null) {
    if (origin != null) {
        document.querySelector(origin).value = '';
        idRequirementType = 11;
    }
    else {
        document.querySelector(selectorAplicationType).value = '';
        $(selectorAplicationType).attr('disabled', true);
        document.querySelector(selectorIdState).value = '';
        $('.states').addClass('d-none');
        document.querySelector(selectorIdCity).value = '';
        $('.cities').addClass('d-none');
    }
    const setFilter = fieldsInForm[idRequirementType](selectorAplicationType, selectorIdState, selectorIdCity, origin)
}

const filedsByAplicationType = {
    '1': (idAplicationType, selectorIdState, selectoIdCity, origin) => {
        // federal
        // code
    },
    '2': (idAplicationType, selectorIdState, selectoIdCity, origin) => {
        // state
        $('.states').removeClass('d-none')
        $(selectorIdState).attr('data-rule-required', true)
        $(selectorIdState).attr('data-msg-required', 'Este campo es obligatorio')
    },
    '4': (idAplicationType, selectorIdState, selectoIdCity, origin) => {
        // local
        $('.states').removeClass('d-none')
        $(selectorIdState).attr('data-rule-required', true)
        $(selectorIdState).attr('data-msg-required', 'Este campo es obligatorio')
        $('.cities').removeClass('d-none')
        $(selectoIdCity).attr('data-rule-required', true)
        $(selectoIdCity).attr('data-msg-required', 'Este campo es obligatorio')
    },
    '5': (idAplicationType, selectorIdState, selectoIdCity, origin) => {
        // corporate
        // code
    },
    '6': (idAplicationType, selectorIdState, selectoIdCity, origin) => {
        // condition
        $('.permission').removeClass('d-none')
        $(origin).attr('data-rule-required', true)
        $(origin).attr('data-msg-required', 'Este campo es obligatorio')
    },
    '7': (idAplicationType, selectorIdState, selectoIdCity, origin) => {
        // inspection doc
        $('.permission').removeClass('d-none')
        $(origin).attr('data-rule-required', true)
        $(origin).attr('data-msg-required', 'Este campo es obligatorio')
    }
}
/**
* Set states 
*/
function setStates(idAplicationType, selectorIdState, selectorIdCity, origin ){
    if (origin) {
        $('.permission').addClass('d-none')
        $('#IdObligation').attr('data-rule-required', true)
        $('#IdObligation').attr('data-msg-required', 'Este campo es obligatorio')
    }
    else {
        $('.states').addClass('d-none')
        $(selectorIdState).attr('data-rule-required', false)
        $(selectorIdState).attr('data-msg-required', '')
        $('.cities').addClass('d-none')
        $(selectorIdCity).attr('data-rule-required', false)
        $(selectorIdCity).attr('data-msg-required', '')
    }
    if (idAplicationType != 11) {
        const setFilter = filedsByAplicationType[idAplicationType](selectorIdState, selectorIdCity, origin)
    }
}
/**
 * Set 
 */
function setSpecificDocument(idEvidence, idSelectorDocument){
    let evidence = 4;
    if (idEvidence == evidence) {
        $('.specificDocument').removeClass('d-none');
        $(idSelectorDocument).attr('data-rule-required', true);
    }
    else {
        document.querySelector(idSelectorDocument).value = '';
        $('.specificDocument').addClass('d-none');
        $(idSelectorDocument).attr('data-rule-required', false);
    }
}
</script>