<script>
function setNoCompliace(){
    const data = corporateData.charts.noCompliance;
    initNoCompliace(data, 'noComplianceActionCanvas');
    initNoCompliace(data, 'noComplianceBarAuditCanvas');
}
function initNoCompliace(data, idCanvas){
    const contextCanvas = document.getElementById(idCanvas).getContext("2d");
    objectCanvas = new Chart(contextCanvas, {
        type: 'bar',
        data:{
            labels: data['labels'],
            datasets: [
                {
                    backgroundColor: data['colors'],
                    data: data['values']
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { 
                display: false 
            },
            title: {
                display: true,
                text: 'No. Cumpliemiento'
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Hallazgos por estatus'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Número de Hallazgos'
                    },
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1,
                        max: data['max']
                    }
                }]
            },
        }
    });
}
</script>