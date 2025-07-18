<div id="perfilImgModal" class="imgCropModal modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="logosModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleLogo" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" onclick="closeCrop()" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="inputlogo" style="min-height:485px;" ></div>
                <div class="selection d-none" >
                    <div class="cropWrap row">
                        <div class="col-sm-8 col-md-8 col-lg-8" >
                            <div id="crop" class="icropper"></div>
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1"></div>
                        <div class="col-sm-3 col-md-3 col-lg-3">
                            <div id="cropPreview" class="previewSmall"></div>
                        </div>
                        <div class="col-sm-10 col-md-10 col-lg-10 sp-10">
                            <div id="infoMe" class="col-sm-12 col-md-12 col-lg-12 border-logos" ></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <button id="saveImgProfile" onclick="saveImgProfile()" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </div>            
                </div>
            </div>
        </div>
    </div>
</div>