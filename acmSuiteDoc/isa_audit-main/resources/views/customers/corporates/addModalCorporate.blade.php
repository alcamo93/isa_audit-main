<div id="addModalCorporate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Planta</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setCorporateForm" method="POST" action="{{ asset('/corporates/set') }}">
                <input type="hidden" id="idCustHidden-s" name="idCustHidden-s" value="{{ $idCustomer }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Planta<span class="star">*</span></label>
                                <input id="s-tradename" name="s-tradename" type="text" class="form-control" placeholder="Nombre de la Planta" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Razón social<span class="star">*</span></label>
                                <input id="s-trademark" name="s-trademark" type="text" class="form-control" placeholder="Nombre fiscal"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>RFC<span class="star">*</span></label>
                                <input id="s-rfc" name="s-rfc" type="text" class="form-control" placeholder="Introduce 13 dígitos" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="13" data-msg-maxlength="Máximo 13 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="s-idStatus" name="s-idStatus" class="form-control"
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
                                <select id="s-type" name="s-type" class="form-control"
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
                                <select 
                                    id="s-idIndustry" 
                                    name="s-idIndustry" 
                                    class="form-control"
                                    title="Selecciona una opción"
                                    onchange="showNewInd(this)"
                                    required
                                >
                                    <option value=""></option>
                                    <option value="0">Otro</option>
                                    @foreach($industries as $element)
                                        <option value="{{ $element['id_industry'] }}">{{ $element['industry'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 d-none newIndustry">
                            <div class="form-group">
                                <label>Nuevo Giro<span class="star">*</span></label>
                                <input id="n-ind" name="n-ind" type="text" class="form-control" placeholder="Nombre del giro" 
                                    data-rule-required="false" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnAddCorporate" href="#" class="btn btn-primary float-right">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>