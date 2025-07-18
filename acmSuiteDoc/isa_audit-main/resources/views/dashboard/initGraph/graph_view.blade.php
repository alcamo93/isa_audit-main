<div class="corporate-preview d-none">
{{-- Primer bloque  --}}
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <button class="btn btn-primary float-right" onclick="scsDashboard()" >Volver</button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="card card-stats border border-primary border-r-25">
                <div class="card-header text-center">
                    <span style="font-weight: 900;">Cumplimiento Legal Global</span>
                    <hr>
                </div>
                <div class="card-body" style="cursor: pointer;">
                    <a onclick="firstIndicator()" data-toggle="tooltip" title="Abrir detalles">
                        <div class="row">
                            <div class="col">
                                <div class="icon-big text-center my-margin">
                                    <img loading="lazy" class="image-matter" src="assets/img/services/s2.png">
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-center">
                                    <h2 class="my-weight" id="globalPercent" ></h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="cards_action_"></div>
    
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-body" style="height:40vh; width:auto">
                    <canvas id="weeaklyPie"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-body" style="height:40vh; width:auto">
                    <canvas id="criticalFindings"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div id="mattersBars" class="row d-flex align-content-start flex-wrap"></div>
    
    <div class="row">
        <div id="noComplianceAction">
            <div class="card">
                <div class="card-body" style="height:40vh; width:auto">
                    <canvas id="noComplianceActionCanvas"></canvas>
                </div>
            </div>
        </div>
        <div id="riskRadarAction">
            <div class="card">
                <div class="card-body" style="height:40vh; width:auto">
                    <canvas id="radarAction"></canvas>
                </div>
            </div>
        </div>
    </div>

{{-- Bloque Auditoria --}}
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card toggle-seccion">
                <div class="card-body text-center">
                    <button
                        id="toggleAudit"
                        class="btn btn-primary btn-fill btn-round btn-icon float-left"
                        data-toggle="tooltip" data-original-title="Esconder auditoria"
                        >
                        <i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>
                    </button>
                    <h6> Auditoria </h6>
                </div>
            </div>
        </div>
    </div>
    <div class="audit">
        <div class="row">
            <div class="col-xl-12 col-md-12 col-12">
                <div class="card card-stats border border-r-25 border-primary">
                    <div class="card-header text-center">
                        <span style="font-weight: 900;">Cumplimiento de auditoria</span>
                        <hr>
                    </div>
                    <div class="card-body" style="cursor: pointer;">
                        <a onclick="firstIndicator()" data-toggle="tooltip" title="" data-original-title="Abrir detalles">
                            <div class="row">
                                <div class="col">
                                    <div class="icon-big text-center my-margin">
                                        <img loading="lazy" class="image-matter" src="assets/img/services/s2.png">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="text-center">
                                        <h2 class="my-weight" id="percent-total-audit" class=""></h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="cards_audit_"></div>
        
        <div class="row">
            <div id="findingsAudit">
                <div class="card" style="height:40vh; width:auto">
                    <a onclick="chartConditions()" data-toggle="tooltip" title="" data-original-title="Abrir detalles">
                        <div class="card-body text-primary"><!-- ml-auto mr-auto-->
                            <center>
                                <h2 class="my-weight-second" id="finding_totals_"></h2>
                                <h4 >Hallazgos</h4>
                            </center>
                        </div>
                    </a>
                </div>
            </div>
            <div id="riskRadarAudit">
                <div class="card" data-step="3">
                    <div class="card-body" style="height:40vh; width:auto">
                        <canvas id="radarAudit"></canvas>
                    </div>
                </div>
            </div>
            <div id="noComplianceBarAudit">
                <div class="card" data-step="3">
                    <div class="card-body" style="height:40vh; width:auto">
                        <canvas id="noComplianceBarAuditCanvas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div id="risksCategories" class="row d-flex align-content-start flex-wrap"></div>
    </div>
    
{{-- Bloque doble --}}
    <div class="row">
        <div class="col d-none" id="LegSection">
            <div class="card toggle-seccion">
                <div class="card-body text-center">
                    <button
                        id="toggleLegs"
                        class="btn btn-primary btn-fill btn-round btn-icon float-left"
                        data-toggle="tooltip" data-original-title="Esconder Legislaciones"
                        >
                        <i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>
                    </button>
                    <h6> Actualizacion en legislación </h6>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12 legs">
                    <div class="card">
                        <div class="card-body" id="card_body_leg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- Bloque Permisos Relevantes --}}
    <div class="row" hidden>
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card text-center toggle-seccion">
                <div class="card-body">
                    <button
                        id="toggleCritics"
                        class="btn btn-primary btn-fill btn-round btn-icon float-left"
                        data-toggle="tooltip" data-original-title="Esconder estatus de permisos relevantes"
                        >
                        <i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>
                    </button>
                    <h6> Estatus permisos relevantes </h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row critics" hidden>
        <div class="col-12 col-md-6 col-lg-1 matter-1 d-none"></div>
        <div class="col-12 col-md-6 col-lg-10 matter-1 d-none">
            <div class="card" data-step="2" data-intro="Grafica 1" data-position='right' data-scrollTo='tooltip'>
                <div class="card-body"><!-- ml-auto mr-auto-->
                    <canvas id="bar1"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-1 matter-1 d-none"></div>
        <div class="col-12 col-md-6 col-lg-1 matter-2 d-none"></div>
        <div class="col-12 col-md-6 col-lg-10 matter-2 d-none">
            <div class="card" data-step="3" data-intro="Grafica 2" data-position='left' data-scrollTo='tooltip'>
                <div class="card-body"><!-- ml-auto mr-auto-->
                    <canvas id="bar2"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-1 matter-2 d-none"></div>
        <div class="col-12 col-md-6 col-lg-1 matter-3 d-none"></div>
        <div class="col-12 col-md-6 col-lg-10 matter-3 d-none">
            <div class="card" data-step="4" data-intro="Grafica 3" data-position='left' data-scrollTo='tooltip'>
                <div class="card-body">
                    <canvas id="bar3"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-1 matter-3 d-none"></div>
    </div>

{{-- Bloque semanal --}}
    <div class="row" hidden>
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card toggle-seccion">
                <div class="card-body text-center">
                    <button
                        id="toggleWeekly"
                        class="btn btn-primary btn-fill btn-round btn-icon float-left"
                        data-toggle="tooltip" data-original-title="Esconder estatus semanal"
                        >
                        <i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>
                    </button>
                    <h6> Estatus semanal </h6>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="row weekly" hidden>
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card" data-step="4" data-intro="Grafica 3" data-position='left' data-scrollTo='tooltip'>
                <div class="card-body">
                    <canvas id="bar4"></canvas>
                </div>
            </div>
        </div>
    </div> -->
</div>
