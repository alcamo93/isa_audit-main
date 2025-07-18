<script>
/**
 * Get data requiremnts
 */
function getAllDataRequirement(idRequirement){
    return new Promise((resolve, reject) => {
        $.get(`/catalogs/requirements/requirements/view`, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idRequirement: idRequirement
        },
        data => {
            resolve(data);
        })
        .fail(e => {
            reject(e.statusText);
        })   
    });
}
/**
 * set Data in modal
 */
function showDataRequirement(idRequirement) {
    getAllDataRequirement(idRequirement)
    .then(data => {
        if (data.length == 0) {
            toastAlert('Por algun motivo no se encuntra información del requerimiento test', 'error');
            return;
        }
        const spec = data[0];
        document.querySelector('#customer-spechtml').innerHTML = spec.cust_trademark || 'N/A';
        document.querySelector('#corporate-spechtml').innerHTML = spec.corp_tradename || 'N/A';
        document.querySelector('#matter-spechtml').innerHTML = spec.matter || 'N/A';
        document.querySelector('#aspect-spechtml').innerHTML = spec.aspect || 'N/A';
        // document.querySelector('#evidence-spechtml').innerHTML = spec.evidence || 'N/A';
        if (spec.document) {
            $('.documentSpec').removeClass('d-none');
            document.querySelector('#documentSpec-spechtml').innerHTML = spec.document || 'N/A';    
        } else $('.documentSpec').addClass('d-none');
        // document.querySelector('#obtainingPeriod-spechtml').innerHTML = spec.period_obtaining || 'N/A';
        // document.querySelector('#updatePeriod-spechtml').innerHTML = spec.period_update || 'N/A';
        document.querySelector('#orderReq-spechtml').innerHTML = spec.order || 'N/A';
        document.querySelector('#aplicationType-spechtml').innerHTML = spec.application_type || 'N/A';
        // document.querySelector('#obligation-spechtml').innerHTML = spec.audit_processes
        document.querySelector('#noRequirement-spechtml').innerHTML = spec.no_requirement || 'N/A';
        document.querySelector('#requirement-spechtml').innerHTML = spec.requirement || 'N/A';
        document.querySelector('#requirementDesc-spechtml').innerHTML = spec.description || 'N/A';
        // document.querySelector('#requirementHelp-spechtml').innerHTML = spec.help_requirement || 'N/A';
        // document.querySelector('#requirementAcceptance-spechtml').innerHTML = spec.acceptance || 'N/A';
        $('#fullRequirementSpecModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        toastAlert(e, 'error');
    });
}
</script>