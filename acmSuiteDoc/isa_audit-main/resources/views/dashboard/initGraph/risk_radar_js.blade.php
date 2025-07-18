<script>
/**
 * Section Action Plan
 */
function drawRadarRisk() {
    const data = corporateData.charts.risk_radar_audit;

    $('#noComplianceAction').removeClass();
    $('#riskRadarAction').removeClass();
    if (data['data'].length != 0) {
        $('#noComplianceAction').addClass('col-12 col-md-6 col-lg-6');
        $('#riskRadarAction').addClass('col-12 col-md-6 col-lg-6');
        $('#riskRadarAction').removeClass('d-none');
        initRadarRisk(data, 'radarAction');
    } else {
        $('#riskRadarAction').addClass('d-none');
        $('#noComplianceAction').addClass('col-12 col-md-12 col-lg-12');
    }
    /**
     * Section Audit
     */
    $('#findingsAudit').removeClass();
    $('#riskRadarAudit').removeClass();
    $('#noComplianceBarAudit').removeClass();
    if (data['data'].length != 0) {
        $('#riskRadarAudit').removeClass('d-none');
        $('#findingsAudit').addClass('col-12 col-md-4 col-lg-2');
        $('#riskRadarAudit').addClass('col-12 col-md-4 col-lg-5');
        $('#noComplianceBarAudit').addClass('col-12 col-md-4 col-lg-5');
        initRadarRisk(data, 'radarAudit');
    } else {
        $('#findingsAudit').addClass('col-12 col-md-6 col-lg-6');
        $('#noComplianceBarAudit').addClass('col-12 col-md-6 col-lg-6');
        $('#riskRadarAudit').addClass('d-none');
    }
}

function initRadarRisk(data, idCanvas) {
    const contextCanvas = document.getElementById(idCanvas).getContext("2d");
    radarObject = new Chart(contextCanvas, {
        type: 'radar',
        data: {
            labels: data['labels'],
            datasets: data['data']
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                text: 'Nivel de riesgo'
            }
        }
    });
}
</script>