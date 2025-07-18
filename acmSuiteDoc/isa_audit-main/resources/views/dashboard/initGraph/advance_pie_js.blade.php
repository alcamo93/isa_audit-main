<script>
/**
 * Draw charts action plan and audit
 */
function setAdvancePie() {
    const data = corporateData.charts.weekly;
    const weeaklyPie = document.getElementById("weeaklyPie").getContext("2d");
    const dataWeeaklyPie = {
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
                        openModalStatusTable(context);
                    }
                }
            }
        }]
    };

    myPieChart = new Chart(weeaklyPie, {
        type: 'pie',
        data: dataWeeaklyPie,
        options: {
            animation:{
                animateScale: true
            },
            responsive: true,
            maintainAspectRatio: false,
            title:{
                display: true,
                text: "Estatus de Action Plan",
            },
            // tooltips: {
            //     mode: 'nearest',
            //     callbacks: {
            //         label: function(tooltipItem, data) {
            //             return corporate.weekly.totalWeeklyDoneTasks+'/'+corporate.weekly.totalWeeklyTasks+' tareas hechas';//data['labels'][tooltipItem['index']];// + ': ' + data['datasets'][0]['data'][tooltipItem['index']] + '%';
            //         }
            //     },
            // },
            legend: {
                position: 'right',
                display: true,
            }
        }
    });
}
/**
 * set header tables of weeaklyPie
 */
function openModalStatusTable(type) {
    let head = '';
    let title = '';
    $('#table_status_chart thead').find('tr').remove();
    if(type.dataIndex == 0) {
        title = 'Actividades completadas.';
        head = `<tr>
                    <th scope="col">No.</th>
                    <th scope="col">Actividad</th>
                    <th scope="col">Responsable</th>
                    <th scope="col">Status</th>
                </tr>`;
    }
    else {
        title = 'Actividades Pendientes.';
        head = `<tr>
                    <th scope="col">No.</th>
                    <th scope="col">Actividad</th>
                    <th scope="col">Responsable</th>
                    <th scope="col">Status</th>
                    <th scope="col">Porcentaje de avance</th>
                </tr>`;
    }
    drawTableStatus(type.dataIndex)
    $('#table_status_chart thead').append(head);
    document.querySelector('#status_modal_chart_title').innerHTML = title;
    $('#status_modal_chart').modal('show');
}
/**
 * set body tables of weeaklyPie
 */
function drawTableStatus(type){
    let actionPlan = corporateData.global.actions;
    let html = '';
    $('#table_status_chart tbody').find('tr').remove();
    actionPlan.forEach(ap => {    
        if(type != 0){
            if(ap['status'] != 'Completado') { //table: c_status, id_status: 17
                let total = 0;
                if (ap['total_tasks'] != 0) {
                    total = (100 * ap['done_tasks']) / ap['total_tasks'];
                }
                html += `<tr>
                    <th scope="col">${ap['num']}</th>
                    <th scope="col text-justify">${ap['description']}</th>
                    <th scope="col text-center">${( (ap['complete_name'] == null) ? '-' : ap['complete_name'])}</th>
                    <th scope="col text-center">${ap['status']}</th>
                    <th scope="col text-center">${total}%</th>
                </tr>`;
            }
        }
        else {
            if(ap['status'] == 'Completado') { //table: c_status, id_status: 17
                html += `<tr>
                    <th scope="col">${ap['num']}</th>
                    <th scope="col text-justify">${ap['description']}</th>
                    <th scope="col text-center">${( (ap['complete_name'] == 'null') ? '' : ap['complete_name'])}</th>
                    <th scope="col text-center">${ap['status']}</th>
                </tr>`;
            }
        }
    });
    $('#table_status_chart tbody').append(html);
}
</script>