<script>
/**
 * Get data subrequiremnts
 */
function getAllDataSubrequirement(idSubrequirement){
    return new Promise((resolve, reject) => {
        $.get(`/catalogs/requirements/subrequirements/view`, {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idSubrequirement: idSubrequirement
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
function showDataFullSubrequirement(idSubrequirement) {
    getAllDataSubrequirement(idSubrequirement)
    .then(data => {
        if (data.length == 0) {
            toastAlert('Por algun motivo no se encuntra información del subrequerimiento', 'error');
            return;
        }
        const sub = data[0];
        document.querySelector('#condition-shtml').innerHTML = sub.condition || 'N/A';
        document.querySelector('#obtainingPeriod-shtml').innerHTML = sub.period_obtaining || 'N/A';
        document.querySelector('#updatePeriod-shtml').innerHTML = sub.period_update || 'N/A';
        document.querySelector('#orderSub-shtml').innerHTML = sub.order || 'N/A';
        document.querySelector('#evidence-shtml').innerHTML = sub.evidence || 'N/A';
        if (sub.document) {
            $('.documentSub').removeClass('d-none');
            document.querySelector('#subDocument-shtml').innerHTML = sub.document || 'N/A';
        } else $('.documentSub').addClass('d-none');
        document.querySelector('#requirementType-shtml').innerHTML = sub.requirement_type || 'N/A';
        document.querySelector('#noSubrequirement-shtml').innerHTML = sub.no_subrequirement || 'N/A';
        document.querySelector('#subrequirement-shtml').innerHTML = sub.subrequirement || 'N/A';
        document.querySelector('#subrequirementDesc-shtml').innerHTML = sub.description || 'N/A';
        document.querySelector('#subrequirementHelp-shtml').innerHTML = sub.help_subrequirement || 'N/A';
        document.querySelector('#subrequirementAcceptance-shtml').innerHTML = sub.acceptance || 'N/A';
        $('#fullSubrequirementModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        toastAlert(e, 'error');
    });
}
</script>