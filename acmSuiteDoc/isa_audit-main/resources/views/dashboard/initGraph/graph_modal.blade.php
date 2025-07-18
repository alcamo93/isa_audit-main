<!--  _______________________________________________________________-->
<!--  __________________  Global percent   __________________________-->
<!--  _______________________________________________________________-->
<div id="globalDetails" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12 col-md-12 col-lg-12 d-flex flex-column">
                    <div class="p-2">
                        <div class="d-flex flex-row">
                            <div class="mr-auto p-2">
                                <h5 class="modal-title" id="globalDetailsTitle">Reporte auditoria</h5>
                            </div>
                            <div class="p-2">
                                <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="p-2" id="button_excel_report"></div>
                        </div>
                        <div class="col">
                            <div class="p-2">
                                <button class="btn btn-primary btn-fill float-bottom float-right"
                                    data-toggle="tooltip" data-original-title="Ir a Auditoría"
                                    onclick="goToAudit()">
                                    Auditoría
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <!-- area collapse -->
                <table class="table table-hover table-striped">
                    <thead>
                        <th>Nombre de Hallazgos</th>
                        <th>Descripción</th>
                        <th class="text-center">Estatus</th>
                        <th class="text-center">Fecha Limite</th>
                        <th>Nivel de Riesgo</th>
                    </thead>
                    <tbody id="globalReport"></tbody>
                </table>
                <!-- end area collapse -->
            </div>
        </div>
    </div>
</div>
<!--  _______________________________________________________________-->
<!--  __________________  Percent Matters   __________________________-->
<!--  _______________________________________________________________-->
<div id="apectsDetails" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase" id="apectsDetailsTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="body_percent_matters">
                
            </div>
        </div>
    </div>
</div>
<!--  _______________________________________________________________-->
<!--  __________________  Details Aspects  __________________________-->
<!--  _______________________________________________________________-->
<div id="aspectsAction" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase" id="aspectsActionTitle"></h5>
                <button type="button" class="close" onclick="closeActionAspects()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- area collapse -->
                <table class="table table-hover table-striped">
                    <thead>
                        <th>Nombre de Hallazgos</th>
                        <th>Descripción</th>
                        <th class="text-center">Estatus</th>
                        <th class="text-center">Fecha Limite</th>
                        <th>Nivel de Riesgo</th>
                    </thead>
                    <tbody id="aspectsActionTable"></tbody>
                </table>
                <!-- end area collapse -->
            </div>
        </div>
    </div>
</div>
<!--  _______________________________________________________________-->
<!--  __________________  Details Aspects  __________________________-->
<!--  _______________________________________________________________-->
<div id="modalChartCondition" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase" id="modalChartConditionTitle">HALLAZGOS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height:40vh; width:auto">
                <canvas id="conditionPie"></canvas>
            </div>
        </div>
    </div>
</div>

<!--  _______________________________________________________________-->
<!--  __________________  Percent Matters   __________________________-->
<!--  _______________________________________________________________-->
<div id="apectsConditions" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase" id="apectsConditionsTitle"></h5>
                <button type="button" class="close" onclick="closeAspectsConditions()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="apectsConditionsBody">
                
            </div>
        </div>
    </div>
</div>
<!--  _______________________________________________________________-->
<!--  __________________  Details Aspects  __________________________-->
<!--  _______________________________________________________________-->
<div id="aspectsActionConditions" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase" id="aspectsActionConditionsTitle"></h5>
                <button type="button" class="close" onclick="closeAspectsActionConditions()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- area collapse -->
                <table class="table table-hover table-striped">
                    <thead>
                        <th>Nombre de Hallazgos</th>
                        <th>Descripción</th>
                        <th class="text-center">Estatus</th>
                        <th class="text-center">Fecha Limite</th>
                        <th>Nivel de Riesgo</th>
                    </thead>
                    <tbody id="aspectsActionConditionsTable"></tbody>
                </table>
                <!-- end area collapse -->
            </div>
        </div>
    </div>
</div>
<!--/////////////////////-->
<!--  Requirements modal -->
<!--/////////////////////-->
<div id="dinamicModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12 col-md-12 col-lg-12 d-flex flex-column">
                    <div class="p-2">
                        <div class="d-flex flex-row">
                            <div class="mr-auto p-2">
                                <h5 class="modal-title" id="dinamicModalTitle"></h5>
                            </div>
                            <div class="p-2">
                                <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-2">
                        <a
                            class="btn btn-primary btn-fill float-bottom float-right"
                            data-toggle="tooltip" data-original-title="ir a auditoría"
                            href="/audit"
                            >
                            Auditoría
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <!-- area collapse -->
                <table class="table table-hover table-striped">
                    <thead>
                        <th>Requerimiento</th>
                        <th>Subrequerimiento</th>
                        <th>Fecha limite</th>
                    </thead>
                    <tbody id="matterRequirements" ></tbody>
                </table>
                <!-- end area collapse -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="status_modal_chart" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="status_modal_chart_title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <table class="table table-hover table-sm" id="table_status_chart">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
</div>
