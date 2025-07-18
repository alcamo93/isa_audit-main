<script>
/**
 * current page requirement
 */
function currentPageReq(idRequirement, selector){
    let current = requirements.data.filter( e => e.id_requirement == idRequirement );
    document.querySelector(selector).innerHTML = current[0].no_requirement;
}
/**
 * curren page basis req or sub
 */
function currentPageBasis(id, origin, page) {
    let selectorLabel = `#noReqTitleLabel-p${page}`;
    let selectorDiv = `#noReqTitle-p${page}`;
    if (origin == 'subrequirements') {
        document.querySelector(selectorLabel).innerHTML = 'Subrequerimiento';
        let current = subrequirements.data.filter( e => e.id_subrequirement == id );
        document.querySelector(selectorDiv).innerHTML = current[0]['no_subrequirement'];
    }
    else { // requirements
        document.querySelector(selectorLabel).innerHTML = 'Requerimiento';
        let current = requirements.data.filter( e => e.id_requirement == id );
        document.querySelector(selectorDiv).innerHTML = current[0]['no_requirement'];
    }
}
</script>