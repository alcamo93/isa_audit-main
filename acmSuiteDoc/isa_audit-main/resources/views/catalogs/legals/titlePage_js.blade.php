<script>
/**
 * Set title basis
 */
function stateBasis(idGuideline) {
    let current = guideline.data.filter( e => e.id_guideline == idGuideline );
    let dateFormat = (current[0]['last_date_format'] != null) ? 
        `<br><span class="font-weight-bold">Última reforma: <span class="font-weight-bold">${current[0]['last_date_format']}</span>` : '';
    let formatString = `<span class="">${current[0]['guideline']}</span><br>
        <span class="font-weight-bold">${current[0]['initials_guideline']}</span>${dateFormat}`;
    document.querySelector('#currentGuideline').innerHTML = formatString;
}
</script>