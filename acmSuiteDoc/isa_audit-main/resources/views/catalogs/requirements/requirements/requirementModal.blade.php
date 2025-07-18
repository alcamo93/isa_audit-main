<div id="requirementModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" id="addRequirementModalIntro">
        <div class="modal-content requirement-Modal">
            <div class="modal-header">
                <h5 id="requirementModalTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.requirement-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="requirementForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                    <div class="col">
                           <div class="form-group">
                                <label>Orden<span class="star">*</span></label>
                                <input 
                                    id="orderReq" 
                                    name="orderReq" 
                                    type="number" 
                                    class="form-control inputs-forms" 
                                    placeholder="Orden" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Evidencia<span class="star">*</span></label>
                                <select 
                                    id="IdEvidence"
                                    name="IdEvidence"
                                    class="form-control inputs-forms"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    onclick="setSpecificDocument(this.value, '#document')"
                                >
                                    <option value=""></option>
                                    @foreach($evidences as $element)
                                        <option value="{{ $element['id_evidence'] }}">{{ $element['evidence'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col specificDocument d-none">
                            <div class="form-group">
                                <label>Documento<span class="star">*</span></label>
                                <input 
                                    id="document" 
                                    name="document"
                                    type="text" 
                                    class="form-control inputs-forms" 
                                    placeholder="Nombre del documento" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="400" 
                                    data-msg-maxlength="Máximo 400 caracteres"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Tipo de condición<span class="star">*</span></label>
                                <select 
                                    id="IdCondition"
                                    name="IdCondition"
                                    class="form-control inputs-forms"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value=""></option>
                                    @foreach($conditions as $element)
                                        <option value="{{ $element['id_condition'] }}">{{ $element['condition'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Periodo de cierre<span class="star">*</span></label>
                                <select
                                    id="IdObtainingPeriod"
                                    name="IdObtainingPeriod"
                                    class="form-control inputs-forms"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value=""></option>
                                    @foreach($periods as $element)
                                        <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Periodo de actualización</label>
                                <select
                                    id="IdUpdatePeriod"
                                    name="IdUpdatePeriod"
                                    class="form-control inputs-forms"
                                    >
                                    <option value="">No aplica</option>
                                    @foreach($periods as $element)
                                        <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Tipos de requerimiento<span class="star">*</span></label>
                                <select 
                                    id="IdRequirementType" 
                                    name="IdRequirementType" 
                                    class="form-control inputs-forms"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    onchange="setAplicationType( this.value, '#IdAplicationType', '#IdState', '#IdCity')"
                                    >
                                    <option value=""></option>
                                    @foreach($requirementTypes as $element)
                                        @if($element['id_requirement_type'] != 11)
                                            <option value="{{ $element['id_requirement_type'] }}">{{ $element['requirement_type'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Competencia<span class="star">*</span></label>
                                <select 
                                    id="IdAplicationType"
                                    name="IdAplicationType"
                                    class="form-control inputs-forms"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    onchange="setStates(this.value, '#IdState', '#IdCity', null)"
                                    required title="Selecciona una opción"
                                    >
                                    <option value=""></option>
                                    @foreach($appTypes as $element)
                                        <option value="{{ $element['id_application_type'] }}">{{ $element['application_type'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col states d-none"> 
                            <div class="form-group">
                                <label>Estado<span class="star">*</span></label>
                                <select 
                                    id="IdState"
                                    name="IdState" 
                                    class="form-control inputs-forms"
                                    onchange="setCities(this.value, '#IdCity')"
                                >
                                    <option value="">Ninguno</option>
                                    @foreach($states as $element)
                                        <option value="{{ $element['id_state'] }}">{{ $element['state'] }}</option>
                                    @endforeach
                                </select> 
                            </div>
                        </div>
                        <div class="col cities d-none"> 
                            <div class="form-group">
                                <label>Ciudad<span class="star">*</span></label>
                                <select 
                                    id="IdCity"
                                    name="IdCity" 
                                    class="form-control inputs-forms"
                                >
                                    <option value="">Ninguno</option>
                                </select> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>N° de requerimiento<span class="star">*</span></label>
                                <input 
                                    id="noRequirement" 
                                    name="noRequirement" 
                                    type="text" 
                                    class="form-control inputs-forms" 
                                    placeholder="Numero de requerimiento" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    rows="3"
                                    />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Requerimiento<span class="star">*</span></label>
                                <textarea 
                                    id="requirement" 
                                    name="requirement" 
                                    type="text" 
                                    class="form-control inputs-forms" 
                                    placeholder="Requerimiento" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea 
                                    id="requirementDesc" 
                                    name="requirementDesc" 
                                    type="text" 
                                    class="form-control inputs-forms" 
                                    placeholder="Descripción" 
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Ayuda del requerimiento</label>
                                <textarea 
                                    id="requirementHelp" 
                                    name="requirementHelp" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Ayuda del requerimiento"
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Criterio de aceptación</label>
                                <textarea 
                                    id="requirementAcceptance" 
                                    name="requirementAcceptance" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Criterio de aceptación"
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer footer-form-req">
                    <button type="submit" id="btnSubmitRequirement" class="btn btn-primary"></button>
                </div>      
            </form>      
        </div>
    </div>
</div>