<div id="addModalBasis" class="modal fade basis-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addBasisModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Artículo</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.basis-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setBasisForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Orden<span class="star">*</span></label>
                                <input id="order_s" name="order_s" type="number"
                                    step=".01" data-msg-step="Máximo dos decimales"
                                    class="form-control" placeholder="Orden" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Artículo<span class="star">*</span></label>
                                <input id="basis_s" name="basis_s" type="text" class="form-control" placeholder="Nombre del Articulo" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Publicar Actualización<span class="star">*</span></label>
                                <select 
                                    id="publish_s" 
                                    name="publish_s" 
                                    class="form-control"
                                    data-rule-required="true"
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value="0" selected>No, solo guardar</option>
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
                                    id="quote_s" 
                                    name="quote_s" 
                                    class="form-control"
                                ></textarea>
                            </div>
                        </div>
                    </div>    
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnAddBasis" href="#" class="btn btn-primary float-right">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>