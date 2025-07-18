<div id="recomendationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addRecomendationModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="recomendationModalTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpRecomendations('.recomendation-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="recomendationForm" method="POST" action="#">
                <div class="modal-body recomendations"></div>
                <div class="modal-footer">
                    <div class="col-sm-12 col-md-12 col-lg-12 addNew">
                    <button id="newRecomendation" class="btn btn-primary ">Agregar nuevo</button>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 showSubmit d-none">
                        <div class="form-group">
                            <label>Recomendación</label>
                            <textarea 
                                id="addRecomendation" 
                                name="addRecomendation" 
                                type="text" 
                                class="form-control" 
                                placeholder="Recomendación"
                                rows="3"
                                ></textarea>
                        </div>
                        <button id="btnSubmitRecomendation" class="btn btn-primary ">Registrar</button>
                    </div>
                </div>      
            </form>  
        </div>
    </div>
</div>

<div id="editRecomendationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="editRecomendationModalTitle" class="modal-title">Editar recomendación</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpRecomendations('.recomendation-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" onclick="closeEdit()" class="close" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="editRecomendationForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="form-group">
                        <textarea 
                            id="editRecomendation" 
                            name="editRecomendation" 
                            type="text" 
                            class="form-control" 
                            rows="7"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <button type="button" onclick="closeEdit()" class="btn btn-primary">Cancelar</button>
                        <button id="btnEditRecomendation" class="btn btn-primary">Guardar</button>
                    </div>
                </div>      
            </form>  
        </div>
    </div>
</div>