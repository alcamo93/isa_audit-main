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
            toastAlert('Por algun motivo no se encuntra información del requerimiento', 'error');
            return;
        }
        const req = data[0];
        document.querySelector('#matter-rhtml').innerHTML = req.matter || 'N/A';
        document.querySelector('#aspect-rhtml').innerHTML = req.aspect || 'N/A';
        document.querySelector('#evidence-rhtml').innerHTML = req.evidence || 'N/A';
        if (req.document) {
            $('.documentView').removeClass('d-none');
            document.querySelector('#document-rhtml').innerHTML = req.document || 'N/A';    
        } else $('.documentView').addClass('d-none');
        document.querySelector('#condition-rhtml').innerHTML = req.condition || 'N/A';
        document.querySelector('#obtainingPeriod-rhtml').innerHTML = req.period_obtaining || 'N/A';
        document.querySelector('#updatePeriod-rhtml').innerHTML = req.period_update || 'N/A';
        document.querySelector('#orderReq-rhtml').innerHTML = req.order || 'N/A';
        document.querySelector('#requirementType-rhtml').innerHTML = req.requirement_type || 'N/A';
        document.querySelector('#aplicationType-rhtml').innerHTML = req.application_type || 'N/A';
        if (req.state) {
            $('.stateView').removeClass('d-none');
            document.querySelector('#state-rhtml').innerHTML = req.state || 'N/A';
        } else $('.stateView').addClass('d-none');
        if (req.city) {
            $('.cityView').removeClass('d-none');
            document.querySelector('#city-rhtml').innerHTML = req.city || 'N/A';
        } else $('.cityView').addClass('d-none');
        document.querySelector('#noRequirement-rhtml').innerHTML = req.no_requirement || 'N/A';
        document.querySelector('#requirement-rhtml').innerHTML = req.requirement || 'N/A';
        document.querySelector('#requirementDesc-rhtml').innerHTML = req.description || 'N/A';
        document.querySelector('#requirementHelp-rhtml').innerHTML = req.help_requirement || 'N/A';
        document.querySelector('#requirementAcceptance-rhtml').innerHTML = req.acceptance || 'N/A';
        $('#requirementModalView').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        toastAlert(e, 'error');
    });
}
</script>