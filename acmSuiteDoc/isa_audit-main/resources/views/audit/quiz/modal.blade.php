<!--Modal basies-->
<div id="basiesModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="basiesTitle" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body">
                <div class="row">
                    <div id="legalText" class="col">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Modal basies end-->
<!--Modal help-->
<div id="helpRequirementModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="helpRequirementTitle" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div id="helpRequirementText" class="modal-body">
                
            </div>
        </div>
    </div>
</div>
<!--Modal help end-->
<!--Modal help-->
<div id="showFindingModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="showFindingTitle" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form id="formFinding" action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Hallazgo<span class="star">*</span></label>
                                <textarea 
                                    id="finding" 
                                    name="finding" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Escriba su Hallazgo" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnRegister" class="btn btn-primary float-right">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--Modal help end-->

<!--Modal help-->
<div id="showSpecificationsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="showSpecificationsTitle" class="modal-title">Especificaciones de nivel de riesgo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div id="showSpecificationsText" class="modal-body list-group">
                
            </div>
        </div>
    </div>
</div>
<!--Modal help end-->

<!--Modal risk-->
<div id="showRiskHelpModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="showRiskHelpTitle" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body">
                
            <div class="row">
                <div class="col">
                    <div class="table-responsive"> 
                        <table id="riskHelpAudit" class="table table-striped table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr class="table-info">
                                    <th class="text-center font-weight-bold text-uppercase" id="captionRiskHelpAudit"colspan="3"></th>
                                </tr>
                                <tr>
                                    <th class="text-center font-weight-bold">NOMBRE</th>
                                    <th class="text-center font-weight-bold">CRITERIO</th>
                                    <th class="text-center font-weight-bold">VALOR</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            </div>
        </div>
    </div>
</div>
<!--Modal help end-->