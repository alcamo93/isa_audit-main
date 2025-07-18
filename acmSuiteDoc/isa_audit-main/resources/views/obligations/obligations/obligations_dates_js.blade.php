<script>
function setDate(title, idObligation, idPeriod) {
    currentAP.idObligation = idObligation;
    currentDates.idPeriod = idPeriod;
    cleanForm('#asignedDateForm');
    $('.loading').removeClass('d-none')
    getDataObligationService()
    .then(data => {
        $('.loading').addClass('d-none');
        document.querySelector('#asignedDateTitle').innerHTML = `Fecha para: ${title}`
        document.querySelector('#updatePeriodText').innerHTML = data.period.period;
        const initDate = (data.init_date) ? formatDate(data.init_date, 'backend') : '';
        const renewalDate = (data.renewal_date) ? formatDate(data.renewal_date, 'date') : 'Sin fecha definida';
        document.querySelector('#s-initDate').value = initDate;
        document.querySelector('#renewalDate').innerHTML = renewalDate;
        $('#asignedDateModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none')
        toastAlert(e, 'error');
    });
}

function calculateDates(initDate){
    $('.loading').removeClass('d-none')
    $.get('/obligations/date/calculate', {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        idPeriod: currentDates.idPeriod,
        initDate: initDate
    },
    (data) => {
        $('.loading').addClass('d-none')
        currentDates.renewalDate = data.renewalDate;
        const renewalDate = formatDate(data.renewalDate, 'date');
        document.querySelector('#renewalDate').innerHTML = renewalDate;
    })
    .fail(e => {
        $('.loading').addClass('d-none')
        toastAlert(e.statusText, 'error');
    });
}
/**
* set date
*/
$('#asignedDateForm').submit( (event) => {
    event.preventDefault() 
    if($('#asignedDateForm').valid()) {
        showLoading('#asignedDateModal')
        //handler notificaction
        $.post('/obligations/date/set', {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            idObligation: currentAP.idObligation,
            initDate: document.querySelector('#s-initDate').value,
            renewalDate: currentDates.renewalDate
        },
        (data) => {
            showLoading('#asignedDateModal')
            toastAlert(data.msg, data.status);
            if (data.status == 'success') {
                reloadObligationsKeepPage();
                $('#asignedDateModal').modal('hide');
            }
        })
        .fail(e => {
            showLoading('#asignedDateModal');
            toastAlert(e.statusText, 'error');
        });
    }
});
</script>