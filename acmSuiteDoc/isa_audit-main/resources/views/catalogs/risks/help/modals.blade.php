<div id="addHelpModal" class="modal fade add-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addHelpModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleAddHelp" class="modal-title">Agregar Ayuda</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setHelpForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Nombre<span class="star">*</span></label>
                                <input id="s-nameHelp" name="s-nameHelp" type="text" class="form-control" placeholder="Nombre"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="250" data-msg-maxlength="Máximo 250 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Valor<span class="star">*</span></label>
                                <input type="number" class="form-control" id="s-value" name="s-value" 
                                    placeholder="1.0" min="0"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-number="true" data-msg-number="Solo se permiten números"
                                    data-rule-min="0" data-msg-min="No puede ser menor a {0}">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Criterio<span class="star">*</span></label>
                                <textarea 
                                    id="s-standard" 
                                    name="s-standard" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Especifica el criterio" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="s-idStatusHelp" name="s-idStatusHelp" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value="" selected disabled>Sin estatus seleccinado</option>
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Valoración de...<span class="star">*</span></label>
                                <select id="s-attribute" name="s-attribute" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value="" selected disabled>Sin estatus seleccinado</option>
                                    @foreach($attributes as $element)
                                        <option value="{{ $element['id_risk_attribute'] }}">{{ $element['risk_attribute'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary ">Registrar</button>
                </div>      
            </form>      
        </div>
    </div>
</div>

<div id="updateHelpModal" class="modal fade update-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="updateHelpModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleUpdateHelp" class="modal-title">Edición de Ayuda</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpUpdate('.update-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="updateHelpForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Nombre<span class="star">*</span></label>
                                <input id="u-nameHelp" name="u-nameHelp" type="text" class="form-control" placeholder="Nombre"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="250" data-msg-maxlength="Máximo 250 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Valor<span class="star">*</span></label>
                                <input type="number" class="form-control" id="u-value" name="u-value" 
                                    placeholder="1.0" min="0"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-number="true" data-msg-number="Solo se permiten números"
                                    data-rule-min="0" data-msg-min="No puede ser menor a {0}">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Criterio<span class="star">*</span></label>
                                <textarea 
                                    id="u-standard" 
                                    name="u-standard" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Especifica el criterio" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="u-idStatusHelp" name="u-idStatusHelp" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value="" selected disabled>Sin estatus seleccinado</option>
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Valoración de...<span class="star">*</span></label>
                                <select id="u-attribute" name="u-attribute" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value="" selected disabled>Sin estatus seleccinado</option>
                                    @foreach($attributes as $element)
                                        <option value="{{ $element['id_risk_attribute'] }}">{{ $element['risk_attribute'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary ">Registrar</button>
                </div>      
            </form>      
        </div>
    </div>
</div>