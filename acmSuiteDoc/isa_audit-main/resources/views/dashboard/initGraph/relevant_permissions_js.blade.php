<script>
function drawRelevantPermission(){
    const data = corporateData.charts.relevant_permissions;
    let canvasBar = '';
    $('#mattersBars').find('div').remove();
    data.forEach(e => {
        canvasBar += `
            <div class="col">
                <div class="card">
                    <div class="card-body" style="height:40vh; width:auto">
                        <canvas id="barMatter${e.idMatter}"></canvas>
                    </div>
                </div>
            </div>`;
    });
    document.getElementById('mattersBars').insertAdjacentHTML('beforeend', canvasBar); 
    data.forEach(init => {
        initMattersBars(init)
    });
}
function initMattersBars(data){
    const idCanvas = `barMatter${data['idMatter']}`;
    const barMatter1 = document.getElementById(idCanvas).getContext("2d");
    chartBarMatter1 = new Chart(barMatter1, {
        type: 'horizontalBar',
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
                text: 'Estatus permisos relevantes para '+data['label']
            },
            scales: {
                xAxes: [{
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
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Aspectos'
                    }
                }]
            },
        }
    });
}
</script>