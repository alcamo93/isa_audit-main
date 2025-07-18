<div id="commentsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="assignedModalIntro4">
        <div class="modal-content">
            <div class="modal-header">
                <p class="modal-title" id="commentsTitle"></p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formComment" class="addCommentInput d-none">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <textarea 
                                    id="addCommment" name="addCommment" type="text"
                                    class="form-control" placeholder="Escriba un comentario" rows="4"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="255" data-msg-maxlength="Máximo 255 caracteres"
                                ></textarea>
                            </div>
                        </div>
                    </div>                    
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger float-right" id="cancelComment" type="button">
                        Cancelar
                    </button>
                    <button class="btn btn-primary float-right" type="submit">
                        Registrar
                    </button>
                </div>
            </form>
            <div class="viewComments">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <ul id="commentsBody" class="list-unstyled">

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary float-right" 
                        type="button" id="addComment">
                        <i class="fa fa-plus-circle fa-lg"></i>
                        Agregar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>