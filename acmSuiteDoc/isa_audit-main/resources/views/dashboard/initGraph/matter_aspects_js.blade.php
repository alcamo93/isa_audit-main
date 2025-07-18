<script>
/**
 * Sections Matters in action plan and audit
 */
function setMatters() {
    $('#cards_audit_').find('div').remove();
    $('#cards_action_').find('div').remove();
    let data = corporateData.matters;
    let htmlAction = '';
    let htmlAudit = '';
    let col = ( parseInt(12) / parseInt(data.length) );
    let percentActionClass = '';
    let percentAuditClass = '';
    data.forEach(e => {
        // html for audit section
        percentActionClass = textColor(e.total_action);
        htmlAction += `
            <div class="col-lg-${col} col-md-${col} col-sm-12">
                <div class="card card-stats border border-r-25" 
                    style="border-color: ${e.color}!important;">
                    <div class="card-header text-center">
                        <span style="font-weight: 900;">${e.matter}</span>
                        <hr>
                    </div>
                    <div class="card-body" style="cursor: pointer;">
                        <a onclick="matterIndicator(${e.id_audit_matter}, 0)" data-original-title="Abrir detalles">
                            <div class="row">
                                <div class="col">
                                    <div class="icon-big text-center my-margin">
                                        <img loading="lazy" class="image-matter" src="${e.image}">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text-center">
                                        <h2 class="my-weight ${percentActionClass}">${Math.round(e.total_action)}%</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>`;
        // html for audit section
        percentAuditClass = textColor(e.total);
        htmlAudit += `
            <div class="col-lg-${col} col-md-${col} col-sm-12">
                <div class="card card-stats border border-r-25" 
                    style="border-color: ${e.color}!important;">
                    <div class="card-header text-center">
                        <span style="font-weight: 900;">${e.matter}</span>
                        <hr>
                    </div>
                    <div class="card-body" style="cursor: pointer;">
                        <a onclick="matterIndicator(${e.id_audit_matter}, 1)" data-original-title="Abrir detalles">
                            <div class="row">
                                <div class="col">
                                    <div class="icon-big text-center my-margin">
                                        <img loading="lazy" class="image-matter" src="${e.image}">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text-center">
                                        <h2 class="my-weight ${percentAuditClass}">${Math.round(e.total)}%</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>`;
    });
    $('#cards_action_').html(htmlAction);
    $('#cards_audit_').html(htmlAudit);
}
/**
 * Show Aspects details
 */
function matterIndicator(idMatterAudit, section) {
    $('#body_percent_matters').find('div').remove();
    let matterIndex = corporateData.matters.findIndex( e => e.id_audit_matter === idMatterAudit );
    let data = corporateData.matters[matterIndex];
    let html = `<div class="row">`;
    let percentClass = '';
    data.aspects.forEach( (e, i) => {
        let totalColor = (section == 0) ? e.total_action : e.total;
        percentClass = textColor(totalColor);
        html += `
            <div class="col">
                <div class="card card-stats border border-primary">
                    <div class="card-header text-center">
                        <span style="font-weight: 900;">${e.aspect}</span>
                        <hr>
                    </div>
                    <a onclick="actionAspects(${idMatterAudit}, ${e.id_audit_aspect})" 
                        data-toggle="tooltip" title="Abrir detalles"
                        style="cursor: pointer;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="icon-big text-center my-margin">
                                        <img loading="lazy" class="image-matter" src="assets/img/services/s2.png">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text-center">
                                        <h2 class="my-weight ${percentClass}">${Math.round( (section == 0) ? e.total_action : e.total )}%</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>`;
        if( ((i+1) % 2) == 0) {
            html += `
                </div>
                    <div class="row">`;
        }
    });
    html += '</div>';
    $('#body_percent_matters').append(html);
    document.querySelector('#apectsDetailsTitle').innerHTML = `ESTATUS-ACTION PLAN: ${data.matter}`;
    $('#apectsDetails').modal({backdrop: 'static', keyboard: false});
}
/**
 * Details actions by aspects
 */
function actionAspects(idMatterAudit, idAuditAspect) {
    $('#apectsDetails').modal('hide');
    let matterIndex = corporateData.matters.findIndex( e => e.id_audit_matter === idMatterAudit );
    let aspectIndex = corporateData.matters[matterIndex].aspects.findIndex( a => a.id_audit_aspect === idAuditAspect );
    let data = corporateData.matters[matterIndex].aspects[aspectIndex];
    // Section Table in modal for action plan and audit in each aspect
    document.querySelector('#aspectsActionTitle').innerHTML = `Hallazgos para el aspecto: ${data.aspect}`;
    $('#aspectsActionTable').find('tr').remove();
    let html = getFormatTable(data.action);
    $('#aspectsActionTable').append(html);
    $('#aspectsAction').modal({backdrop: 'static', keyboard: false});
}
</script>