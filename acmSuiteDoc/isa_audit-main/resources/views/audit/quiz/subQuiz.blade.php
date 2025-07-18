<!-- Show audit by aspect -->
<div class="row sub-audit-card d-none">
    <div class="col">
        <div id="sub-card" class="card card-wizard sub">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <!-- <button type="button" class="close my-close" data-toggle="tooltip" 
                                title="Ayuda" onclick="helpAudit();">
                                <i class="fa fa-question-circle-o"></i>
                            </button> -->
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-success float-right ml-2" data-toggle="tooltip" 
                                title="Regresar" onclick="closeSubAudit();">
                                Regresar
                            </button>
                            <button type="button" class="btn btn-success pull-right ml-2" 
                                onclick="onFinishSubWizard()">
                                Finalizar
                                <i class="fa fa-check-circle fa-lg"></i>
                            </button>
                            <button type="button" class="btn btn-primary float-right" data-toggle="tooltip" 
                                title="Mostrar Faltantes" onclick="evaluateAllSubWizard();">
                                Mostrar Subrequerimientos Faltantes
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Nombre de evaluación</label>
                                <div class="auditTitle"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Alcance</label>
                                <div class="scopeAudit"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Materia</label>
                                <div class="auditMatterTitle"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Aspecto</label>
                                <div class="auditAspectTitle"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="wizardSubForm" action="">
                
                </form>
        </div>
    </div>
</div>

