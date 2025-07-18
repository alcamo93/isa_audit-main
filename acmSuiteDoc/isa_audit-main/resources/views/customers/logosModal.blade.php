<div id="logosModal" class="imgCropModal modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="logosModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleLogo" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpLogos();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" onclick="closeCrop()" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="inputlogo">
                    <input type="hidden" id="idCustLogo" name="idCustLogo">
                    <form class="form" id="formLogo" method="POST" action="#">
                        <div class="row justify-content-md-center">
                            <div class="col-sm-10 col-md-10 col-lg-10 border-logos mb-3">
                                <label for="">Logo</label>
                                <div class="custom-file form-group">
                                    <input type="file" class="custom-file-input required" id="setCustLogo" name="setCustLogo" 
                                        accept="image/*" data-rule-required="true" data-msg-required="Este campo es obligatorio">
                                    <label id="setCustLogoLabel" class="custom-file-label" for="setCustLogo">Elige una imagen</label>
                                </div>
                                <div class="img-preview">
                                    <img src="#" class="img-responsive inline-block" id="setCustLogoPreview" height="65px"/>
                                </div>
                                <div class="col sp-20"></div>
                            </div>
                        </div>
                    </form>
                </div>

                <form id="updateLogo" class="form" method="POST">
                    <div id="cropperLogo" class="d-none">
                        <div class="text-center">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-5">
                                    <label><b>Previsualización</b></label>
                                    <div class="previewLogo" style="margin: 0px auto; overflow: hidden; width: 275px; height: 100px;"></div>
                                </div>
                                <div class="col-sm-10  col-md-10 col-lg-6">
                                    <label><b>Imagen Real</b></label>
                                    <img id="imgLogoCropper" style="width: 100%">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="cropCancel">Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="cropAccept">Aceptar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
