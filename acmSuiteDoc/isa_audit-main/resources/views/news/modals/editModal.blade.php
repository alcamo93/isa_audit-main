<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="editModalIntro" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleEdit" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('#editModalIntro');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updateNewForm" class="form" method="POST" action="{{ asset('/news/update') }}">
                <input type="hidden" id="idNew" name="idNew">
                <div class="modal-body">
                    @php 
                        $modalData = array('word' => 'u-' );
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
                                <label class="redial-font-weight-600">Titular<span class="star">*</span></label>
                                <input type="text" id="u-title" name="u-title" class="form-control" placeholder="Nueva noticia..."
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="255" data-msg-maxlength="Máximo 50 caracteres" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="redial-font-weight-600">Fecha de inicio<span class="star">*</span></label>
                                <input id="u-startDate" name="u-startDate" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                       data-rule-required="true" data-msg-required="La fecha de inicio es obligatoria" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="redial-font-weight-600">Fecha de fin<span class="star">*</span></label>
                                <input id="u-clearDate" name="u-clearDate" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                       data-rule-required="true" data-msg-required="La fecha de fin es obligatoria" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="redial-font-weight-600">Elegir imagen</label>
                                <div class="custom-file">
                                    <input
                                        type="file"
                                        id="u-imgNew"
                                        name="u-imgNew"
                                        class="custom-file-input"
                                        accept="image/*"
                                        />
                                    <label class="custom-file-label" for="customFile">Elegir imagen</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label class="redial-font-weight-600">Contenido<span class="star">*</span></label>
                                <textarea id="u-description" name="u-description" class="form-control" placeholder="Escriba el contenido de la nueva noticia..."
                                          data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                          data-rule-maxlength="500" data-msg-maxlength="Máximo 100 caracteres" ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="u-showComponents" >
                        <label class="col-sm-4 control-label">Componentes a mostrar de noticia</label>
                        <div class="col-sm-4 col-sm-offset-1 checkbox-radios">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input id="u-postTitle" class="form-check-input" type="checkbox">
                                    <span class="form-check-sign"></span>
                                    Titulo
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-4 col-sm-offset-1 checkbox-radios">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input id="u-postContent" class="form-check-input" type="checkbox" >
                                    <span class="form-check-sign"></span>
                                    Contenido
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <img id="u-imgCrop" style="width: 100%">
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="modal-footer">
                    <button id="btnUpdateNew" type="submit" class="btn btn-primary float-right" data-new="0">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>