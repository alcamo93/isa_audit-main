<div id="fileModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="filesModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="fileModalTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAddFiles();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="fileForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Titulo<span class="star">*</span></label>
                                <input id="title-f" name="title-f" type="text" class="form-control" placeholder="Titulo" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Documento<span class="star">*</span></label>
                                <div class="custom-file form-group">
                                    <input type="file" class="custom-file-input required" id="file" name="file" 
                                        accept=".pdf" onchange="fileSelection(this)"
                                        data-rule-required="true" data-msg-required="Este campo es obligatorio">
                                    <label id="fileLabel" class="custom-file-label" for="file"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSubmitFile" class="btn btn-primary ">Registrar</button>
                </div>      
            </form>      
        </div>
    </div>
</div>