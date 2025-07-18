<script>
function drawRiskCategories(){
    let data = corporateData.charts.risk_bars_audit;
    let canvasRisk = '';
    $('#risksCategories').find('div').remove();
    data.forEach(e => {
        canvasRisk += `
            <div class="col">
                <div class="card">
                    <div class="card-body" style="height:40vh; width:auto">
                        <canvas id="riskBarCategory${e.idRiskCategory}"></canvas>
                    </div>
                </div>
            </div>`;
    });
    document.getElementById('risksCategories').insertAdjacentHTML('beforeend', canvasRisk); 
    data.forEach(init => {
        initRiskCategoryBar(init)
    });
}
function initRiskCategoryBar(data){
    const idCanvas = `riskBarCategory${data['idRiskCategory']}`;
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
                text: 'Nivel de '+data['label']
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Clasificación de Riesgo'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Nivel de risgo'
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