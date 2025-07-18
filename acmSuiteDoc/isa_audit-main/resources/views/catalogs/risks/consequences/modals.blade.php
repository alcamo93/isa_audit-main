<div id="addConsequenceModal" class="modal fade add-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addConsequenceModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleAdd" class="modal-title">Agregar Consecuencia</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setConsequenceForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Consecuencia<span class="star">*</span></label>
                                <input id="s-numConsequence" name="s-numConsequence" type="text" class="form-control valid" placeholder="Especificar número" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio" 
                                    data-rule-digits="true" data-msg-digits="Solo se permiten dígitos positivos" data-rule-maxlength="100" 
                                    data-msg-maxlength="Máximo 100 caracteres" aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="s-idStatusC" name="s-idStatusC" class="form-control"
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
                                <label>Descripción de consecuencia<span class="star">*</span></label>
                                    <textarea 
                                    id="s-consequence" 
                                    name="s-consequence" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Consecuencia" 
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

<div id="editConsequenceModal" class="modal fade edit-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="editConsequenceModalIntro">
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
            <form class="form" id="updateConsequenceForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Consecuencia<span class="star">*</span></label>
                                <input id="u-numConsequence" name="u-numConsequence" type="text" class="form-control valid" placeholder="Especificar número" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio" 
                                    data-rule-digits="true" data-msg-digits="Solo se permiten dígitos positivos" data-rule-maxlength="100" 
                                    data-msg-maxlength="Máximo 100 caracteres" aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="u-idStatusC" name="u-idStatusC" class="form-control"
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
                                <label>Descripción de consecuencia<span class="star">*</span></label>
                                    <textarea 
                                    id="u-consequence" 
                                    name="u-consequence" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Consecuencia" 
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


