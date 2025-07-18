<script>
/**
 * set in expired action
 */
function setExpired(title, idActionPlan, closeDate){
    currentAP.idActionPlan = idActionPlan;
    document.querySelector('#expiredTitle').innerHTML = `Vencimiento de Requerimiento No. ${title}`;
    document.querySelector('#requirementName').innerHTML = title;
    document.querySelector('#s-real_close_date').min = formatDate(closeDate, 'backend', 1); 
    $('.loading').removeClass('d-none')
    cleanForm('#expiredForm');
    $('.loading').addClass('d-none');
    $('#expiredModal').modal({backdrop:'static', keyboard: false});  
}
/**
 * Handler to submit set task form 
 */
$('#expiredForm').submit( (event) => {
    event.preventDefault() 
    if($('#expiredForm').valid()) {
        showLoading('#expiredModal');
        setExpiredService()
        .then(data => {
            showLoading('#expiredModal')
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                reloadActionKeepPage();
                reloadExpiredKeepPage();
                $('#expiredModal').modal('hide');
            }
        })
        .catch(e => {
            showLoading('#expiredModal')
            toastAlert(e, 'error');
            console.error(e);
        });
    }
});

/******************************************************************/

/**
 * set in expired action
 */
function setAgainExpired(title, idActionExpired, realCloseDate){
    currentAP.idActionExpired = idActionExpired;
    document.querySelector('#expiredAgainTitle').innerHTML = `Vencimiento de Requerimiento No. ${title}`;
    document.querySelector('#s2-requirementName').innerHTML = title;
    document.querySelector('#s2-real_close_date').min = formatDate(realCloseDate, 'backend', 1); 
    $('.loading').removeClass('d-none')
    cleanForm('#expiredAgainForm');
    $('.loading').addClass('d-none');
    $('#expiredAgainModal').modal({backdrop:'static', keyboard: false});  
}
/**
 * Handler to submit set task form 
 */
$('#expiredAgainForm').submit( (event) => {
    event.preventDefault() 
    if($('#expiredAgainForm').valid()) {
        showLoading('#expiredAgainModal');
        setExpiredAgainService()
        .then(data => {
            showLoading('#expiredAgainModal')
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                reloadExpiredKeepPage();
                $('#expiredAgainModal').modal('hide');
            }
        })
        .catch(e => {
            showLoading('#expiredAgainModal')
            toastAlert(e, 'error');
            console.error(e);
        });
    }
});
</script>