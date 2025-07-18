<div id="requirementModalView" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" id="addRequirementModalIntro">
        <div class="modal-content requirement-Modal">
            <div class="modal-header">
                <h5 id="requirementModalViewTitle" class="modal-title">Información de Requerimiento</h5>
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
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Materia</label>
                            <div id="matter-rhtml"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Aspecto</label>
                            <div id="aspect-rhtml"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Evidencia</label>
                            <div id="evidence-rhtml"></div>
                        </div>
                    </div>
                    <div class="col documentView">
                        <div class="form-group">
                            <label class="font-weight-bold">Documento</label>
                            <div id="document-rhtml"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Tipo de condición</label>
                            <div id="condition-rhtml"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Periodo de cierre</label>
                            <div id="obtainingPeriod-rhtml"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Periodo de actualización</label>
                            <div id="updatePeriod-rhtml"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Orden</label>
                            <div id="orderReq-rhtml"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Tipos de requerimiento</label>
                            <div id="requirementType-rhtml"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="font-weight-bold">Competencia</label>
                            <div id="aplicationType-rhtml"></div>
                        </div>
                    </div>
                    <div class="col stateView"> 
                        <div class="form-group">
                            <label class="font-weight-bold">Estado</label>
                            <div id="state-rhtml"></div> 
                        </div>
                    </div>
                    <div class="col cityView"> 
                        <div class="form-group">
                            <label class="font-weight-bold">Ciudad</label>
                            <div id="city-rhtml"></div> 
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">N° de requerimiento</label>
                            <div id="noRequirement-rhtml"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Requerimiento</label>
                            <div id="requirement-rhtml"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Descripción</label>
                            <div id="requirementDesc-rhtml"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Ayuda del requerimiento</label>
                            <div id="requirementHelp-rhtml"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Criterio de aceptación</label>
                            <div id="requirementAcceptance-rhtml"></div>
                        </div>
                    </div>
                </div>
            </div>   
        </div>
    </div>
</div>