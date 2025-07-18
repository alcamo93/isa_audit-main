<script>
activeMenu(1, 'Tablero de control');
/**
 * Data
 */
const corporateData = {
    global: null,
    auditRegister: null,
    findings: null,
    matters: null,
    legalBasises: null,
    charts: null
}
/**
 * Data Chart
 */
let mixedChart = null;
let myBarChart_03 = null;
let myBarChart_radar_11 = null;
let myBarChart_radar_04 = null;
let myBarChart_bar_01 = null;
let myBarChart_bar_02 = null;
let myBarChart_bar_03 = null;
let myBarChart_bar_04 = null;
let myPieChart = null;
let conditionPie = null;
/**
 * Get contracts by id customer or near finish
 */
function getContracts(idCustomer) {
    $('.loading').removeClass('d-none')
    $.post('/dashboard/corporates', {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        idCustomer
    },
    data => {
        $('.corporates').html(data)
        $('.loading').addClass('d-none')
    })
    .fail(e => {
        $('.loading').addClass('d-none')
    })
}
/**
 * Show customer selection
 */
function scsDashboard() {
    $('.loading').removeClass('d-none')
    $('.corporate-preview').addClass('d-none')
    // clear objects
    corporateData.global = null;
    corporateData.auditRegister = null;
    corporateData.findings.total = null;
    corporateData.matters = null;
    corporateData.legalBasises = null;
    corporateData.charts = null;
    // clear objects chart only used
    if (mixedChart != null) mixedChart.destroy();
    if (myBarChart_03 != null) myBarChart_03.destroy();
    if (myBarChart_radar_11 != null) myBarChart_radar_11.destroy();
    if (myBarChart_radar_04 != null) myBarChart_radar_04.destroy();
    if (myBarChart_bar_01 != null) myBarChart_bar_01.destroy();
    if (myBarChart_bar_02 != null) myBarChart_bar_02.destroy();
    if (myBarChart_bar_03 != null) myBarChart_bar_03.destroy();
    if (myBarChart_bar_04 != null) myBarChart_bar_04.destroy();
    if (myPieChart != null) myPieChart.destroy();
    if (conditionPie != null) conditionPie.destroy();
    setTimeout(() => {
        $('.loading').addClass('d-none')
        $('.customer-selection').removeClass('d-none')
    }, 1000);
}
/**
 * Open audit inside dashboard
 */
function goToAudit(){
    const idAuditRegister = corporateData.global.auditRegister.id_audit_register;
    openAudit(idAuditRegister);
}
/**
 * Open external link audit
 */
function openAudit(idAuditRegister) {
    $('.loading').removeClass('d-none');
    let id = btoa(idAuditRegister);
    window.location.href = `/audit/register/${id}`;
}
/**
 * Show customer info
 */
function showCustomerInfo(idAuditProcess, idActionRegister) {
    $('.loading').removeClass('d-none')
    $('.customer-selection').addClass('d-none')
    $.post('/dashboard/contract', {
        _token: document.querySelector('meta[name="csrf-token"]').content,
        idAuditProcess,
        idActionRegister
    },
    (data) => {
        corporateData.global = data.global;
        corporateData.findings = data.findings;
        corporateData.auditRegister = data.global.auditRegister;
        corporateData.matters = data.matters;
        corporateData.legalBasises = data.legal_basises;
        corporateData.charts = data.other_charts;
        setGlobalData();
        setFindings();
        setMatters();
        setCriticalFindings();
        setNoCompliace();
        drawRadarRisk();
        drawRiskCategories();
        setAdvancePie();
        drawRelevantPermission();
        drawBasises();
        $('.loading').addClass('d-none')
        $('.corporate-preview').removeClass('d-none')
    })
    .fail(e => {
        $('.loading').addClass('d-none')
        $('.customer-selection').removeClass('d-none')
    })
}
/**
 * Set global data in action plan and audit
 */
function setGlobalData() {
    let data = corporateData.global;
    let dataMatters = corporateData.matters;
    // Section Action Plan
    let percentAction = data.total_actions;
    let percentActionClass = textColor(percentAction);
    document.querySelector('#globalPercent').innerHTML = `${Math.round(percentAction)}%`;
    $('#globalPercent').addClass(percentActionClass);
    // Section Audit
    let percentAudit = data.total;
    let percentAuditClass = textColor(percentAudit);
    document.querySelector('#percent-total-audit').innerHTML = `${Math.round(percentAudit)}%`;
    $('#percent-total-audit').addClass(percentAuditClass);
    // Section Table in modal for action plan and audit
    $('#globalReport').find('tr').remove();
    let htmlTemp = '';
    dataMatters.forEach(m => {
        m.aspects.forEach(a => {
            if (a.action.length != 0) {
                htmlTemp += getFormatTable(a.action); 
            }
        });
    });
    let html = htmlTemp;
    $('#globalReport').append(html);
}
/**
 * Details in global
 */
function firstIndicator() {
    let data = corporateData.auditRegister;
    let btnReport = '';
    if (data.id_status == 10) {
        btnReport = `
            <a class="btn btn-primary btn-fill float-bottom float-left" 
                data-toggle="tooltip" title="Generar reporte de Auditoria" 
                href="/audit/report/${data.id_corporate}/${data.id_audit_register}/${data.id_audit_processes}">
                <i class="fa fa-file-excel-o fa-lg"></i>
            </a>`;
    }
    else btnReport = '';
    $('#button_excel_report').html(btnReport);
    $('#globalDetails').modal({backdrop: 'static', keyboard: false});
}
/**
 * Close action aspects
 */
function closeActionAspects() {
    $('#aspectsAction').modal('hide');
    $('#apectsDetails').modal({backdrop: 'static', keyboard: false});
}
/**
 * Set Basies updates
 */
function drawBasises() {
    let data = corporateData.legalBasises;
    if (data.length == 0) {
        $('#LegSection').addClass('d-none');
    }
    else {
        $('#LegSection').removeClass('d-none');
        let html = '<ul class="list-group">';
        data.forEach(e => {
            html += `
            <li class="list-group-item">
                <p style="font-size:18px;padding:0px;" class="font-weight-bold text-justify">${e.guideline}</p>
                <p style="font-size:15px;padding:0px;" class="text-justify">${e.legal_basis}</p>
                <p style="font-size:12px;" class="text-right font-weight-light font-italic">[ Fecha de actualización: ${e.update_format}]</p>
            </li>`;
        });
        html += '</ul>';
        $('#card_body_leg').html(html);
    }
}
/******************** Helpers functions ********************/

/**
 * Evaluate text color global
 */
const textColor = percent => {
    let percentClass = '';
    if ( percent <= 51 ) percentClass = 'text-danger';
    if ( percent < 100 && percent > 51 ) percentClass = 'text-warning';
    if ( percent == 100 ) percentClass = 'text-success';
    return percentClass;
}
/**
 * Set structure table in modals
 */
const getFormatTable = data => {
    let html = '';
    if (data.length == 0) {
        html += `<tr>
            <td class="text-center" colspan="5">No hay Hallazgos para mostrar</td>
        </tr>`
    }
    else {
        data.forEach(e => {
            let format = dataTableAddition(e);
            let risks = '';
            e.risk.forEach(r => {
                risks += `${r.risk_category}: ${r.interpretation}<br>`;
            });
            html += `<tr>
                <td style="width:20px">${e.num}</td>
                <td class="text-justify" style="width:40px">${e.description}</td>
                <td class="text-center" style="width:15px">${e.status}</td>
                <td class="text-center ${format.dateClass}" style="width:15px">${format.closeDate}</td>
                <td class="text-justify" style="width:30px">${risks}</td>
            </tr>`;
        });
    }
    return html;
}
/**
 * Información para tabla global
 */
const dataTableAddition = element => {
    let date = Date.now()
    let lastDate = Date.parse(element.close_date)
    let closeDate = null
    let dateClass = ''

    if(date > lastDate && element.real_close_date ) {
        dateClass = 'text-danger'
        closeDate = element.real_close_date
    }
    else {
        dateClass = 'text-warning'
        closeDate = element.close_date
    }
    if(closeDate) {
        closeDate = closeDate.split(" ")
        closeDate = closeDate[0].split("-")
        closeDate = closeDate[2]+'-'+closeDate[1]+'-'+closeDate[0]
    }
    else closeDate = '-'

    data = {
        closeDate: closeDate,
        dateClass: dateClass
    }
    return data;
}

/************************ Toggle Menu ************************/
    
/**
 * Show/hide news
 */
$('#toggleNews').click((e)=>{
    e.preventDefault()
    if($('.news').hasClass('gone'))
    {
        $('#toggleNews').html('<i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleNews').attr('data-original-title', "Esconder noticias" )
        $('.news').removeClass('gone')
        $('.news').fadeToggle()
    }
    else
    {
        $('#toggleNews').html('<i class="fa fa-chevron-down text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleNews').attr('data-original-title', "Mostrar noticias" )
        $('.news').addClass('gone')
        $('.news').fadeToggle()
    }
})
/**
 * Show/hide critics
 */
$('#toggleCritics').click((e)=>{
    e.preventDefault()
    if($('.critics').hasClass('gone'))
    {
        $('#toggleCritics').html('<i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleCritics').attr('data-original-title', "Esconder estatus de permisos relevantes" )
        $('.critics').removeClass('gone')
        $('.critics').fadeToggle()
    }
    else
    {
        $('#toggleCritics').html('<i class="fa fa-chevron-down text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleCritics').attr('data-original-title', "Mostrar estatus de permisos relevantes" )
        $('.critics').addClass('gone')
        $('.critics').fadeToggle()
    }
})
/**
 * Show/hide audit
 */
    $('#toggleAudit').click((e)=>{
    e.preventDefault()
    if($('.audit').hasClass('gone'))
    {
        $('#toggleAudit').html('<i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleAudit').attr('data-original-title', "Esconder auditoria" )
        $('.audit').removeClass('gone')
        $('.audit').fadeToggle()
    }
    else
    {
        $('#toggleAudit').html('<i class="fa fa-chevron-down text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleAudit').attr('data-original-title', "Mostrar auditoria" )
        $('.audit').addClass('gone')
        $('.audit').fadeToggle()
    }
})
/**
 * Show/hide news
 */
    $('#toggleNews').click((e)=>{
    e.preventDefault()
    if($('.news').hasClass('gone'))
    {
        $('#toggleNews').html('<i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleNews').attr('data-original-title', "Esconder Noticias" )
        $('.news').removeClass('gone')
        $('.news').hide()
    }
    else
    {
        $('#toggleNews').html('<i class="fa fa-chevron-down text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleNews').attr('data-original-title', "Mostrar Noticias" )
        $('.news').addClass('gone')
        $('.news').show()
    }
})
/**
 * Show/hide leg
 */
    $('#toggleLegs').click((e)=>{
    e.preventDefault()
    if($('.legs').hasClass('gone'))
    {
        $('#toggleLegs').html('<i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleLegs').attr('data-original-title', "Esconder Legislaciones" )
        $('.legs').removeClass('gone')
        $('.legs').hide()
    }
    else
    {
        $('#toggleLegs').html('<i class="fa fa-chevron-down text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleLegs').attr('data-original-title', "Mostrar Legislaciones" )
        $('.legs').addClass('gone')
        $('.legs').show()
    }
})
/**
 * Show/hide weekly
 */
$('#toggleWeekly').click((e)=>{
    e.preventDefault()
    if($('.weekly').hasClass('gone'))
    {
        $('#toggleWeekly').html('<i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleWeekly').attr('data-original-title', "Esconder estatus semanal" )
        $('.weekly').removeClass('gone')
        $('.weekly').fadeToggle()
    }
    else
    {
        $('#toggleWeekly').html('<i class="fa fa-chevron-down text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleWeekly').attr('data-original-title', "Mostrar estatus semanal" )
        $('.weekly').addClass('gone')
        $('.weekly').fadeToggle()
    }
})
/**
 * Show/hide corporate preview selection
 */
$('#toggleObligations').click((e)=>{
    e.preventDefault()
    if($('.obligations').hasClass('gone'))
    {
        $('#toggleObligations').html('<i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleObligations').attr('data-original-title', "Esconder obligaciones" )
        $('.obligations').removeClass('gone')
        $('.obligations').fadeToggle()
    }
    else
    {
        $('#toggleObligations').html('<i class="fa fa-chevron-down text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleObligations').attr('data-original-title', "Mostrar obligaciones" )
        $('.obligations').addClass('gone')
        $('.obligations').fadeToggle()
    }
})
/**
 * Show/hide corporate preview selection
 */
$('#toggleCPS').click((e)=>{
    e.preventDefault()
    if($('.corporationPreviewSelection').hasClass('gone'))
    {
        $('#toggleCPS').html('<i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleCPS').attr('data-original-title', "Esconder selección de clientes" )
        $('.corporationPreviewSelection').removeClass('gone')
        $('.corporationPreviewSelection').fadeToggle()
        $('.corporates').fadeToggle()
    }
    else
    {
        $('#toggleCPS').html('<i class="fa fa-chevron-down text-white" style="width: 20px;" aria-hidden="true"></i>')
        $('#toggleCPS').attr('data-original-title', "Mostrar selección de clientes" )
        $('.corporationPreviewSelection').addClass('gone')
        $('.corporationPreviewSelection').fadeToggle()
        $('.corporates').fadeToggle()
    }
})
</script>