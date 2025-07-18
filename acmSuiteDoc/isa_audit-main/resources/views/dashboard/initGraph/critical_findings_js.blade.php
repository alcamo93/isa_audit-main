<script>
function setCriticalFindings(){
    const data = corporateData.charts.critical_find;
    const contextCanvas = document.getElementById("criticalFindings").getContext("2d");
    mixedChart = new Chart(contextCanvas, {
        type: 'bar',
        data: {
            labels: data['labels'],
            datasets: [
                {
                    label: 'Hallazgos criticos',
                    data: data['values'],
                    backgroundColor: data['colors'],
                    order: 1
                },
                {
                    label: '',
                    data: data['values'],
                    type: 'line',
                    order: 2
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
                text: 'Hallazgos criticos'
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Aspectos'
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