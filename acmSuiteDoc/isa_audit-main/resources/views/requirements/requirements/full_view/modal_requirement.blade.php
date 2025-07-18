<div id="fullRequirementSpecModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" id="addRequirementModalIntro">
        <div class="modal-content requirement-Modal">
            <div class="modal-header">
                <h5 id="fullRequirementSpecModalTitle" class="modal-title">Información de Requerimiento</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.requirement-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">    
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Cliente</label>
                            <div id="customer-spechtml"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Planta</label>
                            <div id="corporate-spechtml"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Materia</label>
                            <div id="matter-spechtml"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Aspecto</label>
                            <div id="aspect-spechtml"></div>
                        </div>
                    </div>
                    <!-- <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Evidencia</label>
                            <div id="evidence-spechtml"></div>
                        </div>
                    </div> -->
                    <div class="col documentSpec">
                        <div class="form-group">
                            <label class="font-weight-bold">Documento</label>
                            <div id="documentSpec-spechtml"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Periodo de cierre</label>
                            <div id="obtainingPeriod-spechtml"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Periodo de actualización</label>
                            <div id="updatePeriod-spechtml"></div>
                        </div>
                    </div> -->
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Orden</label>
                            <div id="orderReq-spechtml"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Tipo de aplicación</label>
                            <div id="aplicationType-spechtml"></div>
                        </div>
                    </div>
                    <!-- <div class="col obligationSpec">
                        <div class="form-group">
                            <label class="font-weight-bold">Obligación:</label>
                            <div id="obligation-spechtml"></div>
                        </div>
                    </div>   -->
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">N° de requerimiento</label>
                            <div id="noRequirement-spechtml"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Requerimiento</label>
                            <div id="requirement-spechtml"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Descripción</label>
                            <div id="requirementDesc-spechtml"></div>
                        </div>
                    </div>
                    <!-- <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Ayuda del requerimiento</label>
                            <div id="requirementHelp-spechtml"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Criterio de aceptación</label>
                            <div id="requirementAcceptance-spechtml"></div>
                        </div>
                    </div> -->
                </div>
            </div>   
        </div>
    </div>
</div>