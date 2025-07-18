<!--Modal Evaluation1-->
<div id="dataQuestionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="dataQuestionTitle" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div id="dataQuestionText" class="modal-body">
                
            </div>
        </div>
    </div>
</div>
<!--Modal Evaluation1 end-->

<!--Modal Evaluation1-->
<div id="dataCommentsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="dataCommentsTitle" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body">
                <form class="form" id="setCommentsForm" id-aplicability="">
                    <div class="row">
                        <div class="col media">
                            <div class="media-body form-group">
                                <textarea id="comments" type="text" class="form-control"
                                data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                placeholder="Escribe algun comnatario..."
                                ></textarea>
                            </div>
                        </div>
                    </div>
                    <hr class="divider">
                    <div class="modal-footer">
                        <button id="saveCommentBtn" 
                            type="submit"
                            class="btn btn-primary"
                        >Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Modal Evaluation1 end-->