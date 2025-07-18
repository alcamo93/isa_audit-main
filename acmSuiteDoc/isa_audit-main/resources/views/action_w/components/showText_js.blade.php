<script>
/*************** Show long text AP ***************/
/**
 * Show text 
 */
function showText(title, bodyText, subtitule = true){
    document.querySelector('#showAPTitle').innerHTML =  `<b>${title}</b>`;
    document.querySelector('#showAPText').innerHTML = '';
    let text = '';
    text += (subtitule) ? `<p class="font-weight-bold">Hallazgo:</p>\n` : '';
    text += `<p class="text-justify">${bodyText}</p>`;
    $('#showAPText').append(text);
    $('#showAPModal').modal({backdrop:'static', keyboard: false});
}

function showTextService(title, idActionPlan, subtitule = true){
    document.querySelector('#showAPTitle').innerHTML =  `<b>${title}</b>`;
    document.querySelector('#showAPText').innerHTML = '';
    currentAP.idActionPlan = idActionPlan;
    $('.loading').removeClass('d-none')
    getDataActionService()
    .then(({finding}) => {
        let text = '';
        text += (subtitule) ? `<p class="font-weight-bold">Hallazgo:</p>\n` : '';
        text += `<p class="text-justify">${finding}</p>`;
        $('#showAPText').append(text);
        $('.loading').addClass('d-none')
        $('#showAPModal').modal({backdrop:'static', keyboard: false});
    })
    .catch(e => {
        $('.loading').addClass('d-none')
        toastAlert(e.statusText, 'error');
    });
}
</script>