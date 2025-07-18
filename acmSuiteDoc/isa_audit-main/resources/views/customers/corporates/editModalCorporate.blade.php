<div id="editModalCorporate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="editModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleEditCorporate" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="updateCorporateForm" method="POST" action="{{ asset('/corporates/update') }}">
                <input type="hidden" id="idCustHidden-u" name="idCustHidden-u" value="{{ $idCustomer }}">
                <input type="hidden" id="idCorpHidden" name="idCorpHidden">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Planta<span class="star">*</span></label>
                                <input id="u-tradename" name="u-tradename" type="text" class="form-control" placeholder="Nombre de planta" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Razón social<span class="star">*</span></label>
                                <input id="u-trademark" name="u-trademark" type="text" class="form-control" placeholder="Nombre fiscal"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>RFC<span class="star">*</span></label>
                                <input id="u-rfc" name="u-rfc" type="text" class="form-control" placeholder="Introduce 13 dígitos" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="13" data-msg-maxlength="Máximo 13 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="u-idStatus" name="u-idStatus" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Tipo de planta<span class="star">*</span></label>
                                <select id="u-type" name="u-type" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                    <option value="0">Operativa</option>
                                    <option value="1">Nueva</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Giro<span class="star">*</span></label>
                                <select id="u-idIndustry" name="u-idIndustry" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                    @foreach($industries as $element)
                                        <option value="{{ $element['id_industry'] }}">{{ $element['industry'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnEditCorporate" class="btn btn-primary float-right">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>