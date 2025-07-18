<script>
/**
 * Section Findings
 */
function setFindings() {
    let data = corporateData.findings;
    // Section finding count
    document.querySelector('#finding_totals_').innerHTML = data.total;
}
/**
 * set data in chart for condition findings
 */
function chartConditions() {
    let data = corporateData.findings.chart;
    $('#modalChartCondition').modal({backdrop: 'static', keyboard: false});
    const conditionPieId = document.getElementById("conditionPie").getContext("2d");
    const paramsConditions = {
        labels: data['labels'],
        datasets: [{
            data: data['values'],
            backgroundColor: data['colors'],
            datalabels: {
                color: '#FFFFFF',
                labels: {
                    title: {
                        font: {
                            weight: 'bold',
                            size: 16,
                        }
                    }
                },
                formatter: function(value, context) {
                    return value+'%';
                },
                listeners: {
                    click: function(context) {
                        aspectsConditions(context);
                    }
                }
            }
        }]
    };

    conditionPie = new Chart(conditionPieId, {
        type: 'doughnut',
        data: paramsConditions,
        options: {
            animation:{
                animateScale: true
            },
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'right',
                display: true,
            }
        }
    });
}
/**
 * Aspects for conditions findigs
 */
function aspectsConditions(context) {
    $('#modalChartCondition').modal('hide');
    $('#apectsConditionsBody').find('div').remove();
    let data = corporateData.findings.aspects;
    let aspects = [];
    let html = `<div class="row">`;
    let typeActions = '';
    let typetTitle = '';
    let percentClass = ''; 
    if (context.dataIndex == 0) {
        typeActions = 'critical';
        typetTitle = 'CRITICOS';
    }
    if (context.dataIndex == 1) {
        typeActions = 'operative';
        typetTitle = 'OPERATIVOS';
    }
    data.forEach( (e, i) => {
        percentClass = textColor(e.total);
        if (e[typeActions].length != 0) {
            html += `
                <div class="col">
                    <div class="card card-stats border border-primary">
                        <div class="card-header text-center">
                            <span style="font-weight: 900;">${e.aspect}</span>
                            <hr>
                        </div>
                        <a onclick="actionConditions(${e.id_aspect}, '${e.aspect}', '${typeActions}', '${typetTitle}')" data-toggle="tooltip" title="Abrir detalles">;
                            <div class="card-body">;
                                <div class="row">
                                    <div class="col">
                                        <div class="icon-big text-center my-margin">
                                            <img loading="lazy" class="image-matter" src="assets/img/services/s2.png">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="text-center">;
                                            <h2 class="my-weight ${percentClass}">${Math.round(e.total)}%</h2>
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
        }
    });
    html += '</div>';
    $('#apectsConditionsBody').append(html);
    document.querySelector('#apectsConditionsTitle').innerHTML = `ASPECTOS ${typetTitle}`;
    $('#apectsConditions').modal({backdrop: 'static', keyboard: false});
}
/**
 * Close aspects actions conditions
 */
function closeAspectsConditions() {
    $('#apectsConditions').modal('hide');
    $('#modalChartCondition').modal({backdrop: 'static', keyboard: false});
}
/**
 * Shoe actions conditions
 */
function actionConditions(idAspect, aspectName, typeActions, typetTitle) {
    $('#apectsConditions').modal('hide');
    let data = corporateData.findings.aspects;
    let aspectIndex = data.findIndex( e => e.id_aspect === idAspect );
    let aspect = data[aspectIndex][typeActions];
    // Section Table in modal 
    document.querySelector('#aspectsActionConditionsTitle').innerHTML = `Hallazgos ${typetTitle} del aspecto ${aspectName}`;
    $('#aspectsActionConditionsTable').find('tr').remove();
    let html = getFormatTable(aspect);
    $('#aspectsActionConditionsTable').append(html);
    $('#aspectsActionConditions').modal({backdrop: 'static', keyboard: false});
}
/**
 * Close aspects actions conditions
 */
function closeAspectsActionConditions() {
    $('#aspectsActionConditions').modal('hide');
    $('#apectsConditions').modal({backdrop: 'static', keyboard: false});
}
</script>