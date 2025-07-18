<div id="editModalBasis" class="modal fade edit-basis-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleEditBasis" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.edit-basis-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="updateBasisForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Orden<span class="star">*</span></label>
                                <input id="order_u" name="order_u" type="number" 
                                    step=".01" data-msg-step="Máximo dos decimales"
                                    class="form-control" placeholder="Orden" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Artículo<span class="star">*</span></label>
                                <input id="basis_u" name="basis_u" type="text" class="form-control" placeholder="Nombre del Articulo" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Publicar Actualización<span class="star">*</span></label>
                                <select 
                                    id="publish_u" 
                                    name="publish_u" 
                                    class="form-control"
                                    data-rule-required="true"
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value="0" selected>No, solo actualizar</option>
                                    <option value="1">Si, publicar</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Cita legal<span class="star">*</span></label>
                                <textarea  
                                    id="quote_u" 
                                    name="quote_u" 
                                    class="form-control"
                                ></textarea>
                            </div>
                        </div>
                    </div>      
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnEditBasis" href="#" class="btn btn-primary float-right">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>