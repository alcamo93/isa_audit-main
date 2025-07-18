<!-- Add Task -->
<div id="setFileModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleFile"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setFile">

                <div class="modal-body load-file">
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Categoría<span class="star">*</span></label>
                                <select id="idCategory"
                                        name="idCategory"
                                        class="form-control forBlock"
                                        data-rule-required="true"
                                        data-msg-required="Este campo es obligatorio">
                                    <option value="">Seleccionar categoria</option>
                                    @foreach($categories as $row)
                                        <option value="{{ $row['id_category'] }}">{{ $row['category'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Nombre del documento<span class="star">*</span></label>
                                <input class="form-control forBlock" type="text"
                                       name="nameFile" id="nameFile"
                                       placeholder="Nombre del documento"
                                       data-rule-required="true"
                                       data-msg-required="Este campo es obligatorio"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <label>Cargar documento<span class="star">*</span></label>
                            <div class="custom-file form-group">
                                <input type="file" class="custom-file-input required forBlock" id="idDocumentFile" name="idDocumentFile"
                                       data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                       accept=".zip,.pdf,.rar,.dwg,.xlsx,.xls,.doc,.docx,.xlsm,.pptx,.txt,.jpg,.png,.mp4,.html,.msg,.jpeg,.csv">
                                <label id="idDocumentFileLabel" class="custom-file-label text-truncate">Elige un documento</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body review-file">
                    <div class="blockFile d-none">
                        <div class="row d-flex justify-content-center">
                            <div class="col-sm-10 col-md-9">
                                <p class="text-center text-danger">La tarea ha expirado, no puede modifcar o cargar archivos</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Categoría</label>
                                <div id="categoryFile"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre del documento</label>
                                <div id="titleFile"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12"> 
                            <div class="btn-group btn-group-lg w-100" role="group">
                                <button type="button" id="downloadFile" class="btn btn-info">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                    Descargar
                                </button>
                                <button type="button" id="replaceFile" class="btn btn-primary forBlock">
                                    <i class="fa fa-files-o" aria-hidden="true"></i>
                                    Remplazar
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12"> 
                            <div class="btn-group btn-group-lg w-100" role="group">
                                <button type="button" id="rejectFile" class="btn btn-danger forBlock">
                                    Rechazar
                                </button>  
                                <button type="button" id="completeFile" class="btn btn-success forBlock">
                                    Aprobar
                                </button>  
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary float-right load-file">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>