<div id="aspectsModal" class="modal fade aspects-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="aspectModalTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.aspects-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setAspectsForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-8">
                            <div class="form-group">
                                <label>Aspecto<span class="star">*</span></label>
                                <input 
                                    id="aspect" 
                                    name="aspect" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Aspecto" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Orden<span class="star">*</span></label>
                                <input 
                                    id="order" 
                                    name="order" 
                                    type="number" 
                                    class="form-control" 
                                    placeholder="Aspecto" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnModalAspects" class="btn btn-primary "></button>
                </div>      
            </form>      
        </div>
    </div>
</div>