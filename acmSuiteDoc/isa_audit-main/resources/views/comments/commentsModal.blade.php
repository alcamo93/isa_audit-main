<div id="commentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="commentsModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="commentModalTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAddComments();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="commentForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Titulo<span class="star">*</span></label>
                                <input id="titleC" name="titleC" type="text" class="form-control" placeholder="Titulo" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Comentario<span class="star">*</span></label>
                                <textarea 
                                    id="commentC" 
                                    name="commentC" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Recomendación"
                                    rows="3"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSubmitComment" class="btn btn-primary "></button>
                </div>      
            </form>      
        </div>
    </div>
</div>