<div id="addProbabilityModal" class="modal fade add-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addProbabilityModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleAdd" class="modal-title">Agregar Probabilidad</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setProbabilityForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Probabilidad<span class="star">*</span></label>
                                <input id="s-numProbability" name="s-numProbability" type="text" class="form-control valid" placeholder="Especificar número" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio" 
                                    data-rule-digits="true" data-msg-digits="Solo se permiten dígitos positivos" data-rule-maxlength="100" 
                                    data-msg-maxlength="Máximo 100 caracteres" aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="s-idStatusP" name="s-idStatusP" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value="" selected disabled>Sin estatus seleccinado</option>
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Descripción de probabilidad<span class="star">*</span></label>
                                    <textarea 
                                    id="s-probability" 
                                    name="s-probability" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Probabilidad" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    rows="3"
                                    ></textarea>
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

<div id="editProbabilityModal" class="modal fade edit-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="editProbabilityModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleEditCatgory" class="modal-title">Editar Consecuencia</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.edit-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="updateProbabilityForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Consecuencia<span class="star">*</span></label>
                                <input id="u-numProbability" name="u-numProbability" type="text" class="form-control valid" placeholder="Especificar número" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio" 
                                    data-rule-digits="true" data-msg-digits="Solo se permiten dígitos positivos" data-rule-maxlength="100" 
                                    data-msg-maxlength="Máximo 100 caracteres" aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="u-idStatusP" name="u-idStatusP" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value="" selected disabled>Sin estatus seleccinado</option>
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Descripción de probabilidad<span class="star">*</span></label>
                                    <textarea 
                                    id="u-probability" 
                                    name="u-probability" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Probabilidad" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary float-right">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>


