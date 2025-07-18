<div id="mattersModal" class="modal fade matter-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="matterModalTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.matter-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setMattersForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Materia<span class="star">*</span></label>
                                <input 
                                    id="matter" 
                                    name="matter" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Materia" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Descripción</label>
                                <input 
                                    id="matterDesc" 
                                    name="matterDesc" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Descripción"
                                    />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnModalMatter" class="btn btn-primary "></button>
                </div>      
            </form>      
        </div>
    </div>
</div>