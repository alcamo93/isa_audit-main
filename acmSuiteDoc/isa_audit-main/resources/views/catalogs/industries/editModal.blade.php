<div id="editModal" class="modal fade edit-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="editModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleEdit" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.edit-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="updateIndustryForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Nombre<span class="star">*</span></label>
                                <input id="u-name" name="u-name" type="text" class="form-control" placeholder="Giro" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit"  id="btnEditIndustry" class="btn btn-primary float-right">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>