<div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Noticia</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('#addModalIntro');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="setNewForm" class="form" method="POST" action="{{ asset('/news/set') }}">
                <div class="modal-body">
                    @php 
                        $modalData = array('word' => 's-' );
                    @endphp
                    @switch(Session::get('profile')['id_profile_type'])
                        @case(1) @case(2) 
                            @include('news.modals.filters.owner_modal', $modalData) 
                        @break
                        @case(3) @case(4) @case(5) 
                            @include('news.modals.filters.customer_modal', $modalData) 
                        @break
                    @endswitch
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Titular<span class="star">*</span></label>
                                <input type="text" id="title" name="title" class="form-control" placeholder="Nueva noticia..."
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="255" data-msg-maxlength="Máximo 50 caracteres" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Fecha de inicio<span class="star">*</span></label>
                                <input id="startDate" name="startDate" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                       data-rule-required="true" data-msg-required="La fecha de inicio es obligatoria" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Fecha de fin<span class="star">*</span></label>
                                <input id="clearDate" name="clearDate" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                       data-rule-required="true" data-msg-required="La fecha de fin es obligatoria" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Elegir imagen<span class="star">*</span></label>
                                <div class="custom-file">
                                    <input type="file" id="imgNew" name="imgNew" class="custom-file-input" accept="image/*"
                                        data-rule-required="true" data-msg-required="Este campo es obligatorio"/>
                                    <label class="custom-file-label" for="customFile">Elegir imagen</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Contenido<span class="star">*</span></label>
                                <textarea id="description" name="description" class="form-control" placeholder="Escriba el contenido de la nueva noticia..."
                                          data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                          data-rule-maxlength="500" data-msg-maxlength="Máximo 100 caracteres" ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="showComponents">
                        <label class="col-sm-4 control-label">Componentes a mostrar de noticia</label>
                        <div class="col-sm-4 col-sm-offset-1 checkbox-radios">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input id="postTitle" class="form-check-input" type="checkbox" checked>
                                    <span class="form-check-sign"></span>
                                    Titulo
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-4 col-sm-offset-1 checkbox-radios">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input id="postContent" class="form-check-input" type="checkbox" >
                                    <span class="form-check-sign"></span>
                                    Contenido
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <img id="imgCrop" style="width: 100%">
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="modal-footer">
                    <button id="btnAddNew" type="submit" class="btn btn-primary float-right" data-new="0">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
