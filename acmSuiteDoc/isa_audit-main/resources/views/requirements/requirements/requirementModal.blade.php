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
                    <!-- <div class="row" id="areaFilterCustomerModal"> -->
                    @php 
                        $modalData = array('word' => 's-' );
                    @endphp
                    @switch(Session::get('profile')['id_profile_type'])
                        @case(1) @case(2) 
                            @include('requirements.requirements.modal.owner_modal', $modalData)
                            @break
                        @case(3) 
                            @include('requirements.requirements.modal.customer_modal', $modalData)
                            @break
                        @case(4) @case(5)
                            @include('requirements.requirements.modal.corporative_modal', $modalData)
                            @break
                    @endswitch
                    <!-- </div> -->
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Materia<span class="star">*</span></label>
                                <select
                                    id="IdMatter"
                                    name="IdMatter"
                                    class="form-control inputs-forms"
                                    onchange="setAspects(this.value, '#IdAspect', '', false)"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value=""></option>
                                    @foreach($matters as $element)
                                        <option value="{{ $element['id_matter'] }}">{{ $element['matter'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Aspecto<span class="star">*</span></label>
                                <select 
                                    id="IdAspect" 
                                    name="IdAspect" 
                                    class="form-control inputs-forms"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                >
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <!-- <div class="col">
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
                        </div> -->
                        <!-- <div class="col specificDocument d-none">
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
                                    data-rule-maxlength="100" 
                                    data-msg-maxlength="Máximo 100 caracteres"
                                />
                            </div>
                        </div> -->
                    </div>
                    <div class="row">
                        <!-- <div class="col">
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
                        </div> -->
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
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Tipo de aplicación</label>
                                <select 
                                    id="IdAplicationType"
                                    name="IdAplicationType"
                                    class="form-control inputs-forms"
                                    onchange="setStates(this.value, '#IdState', '#IdCity', '#IdObligation')"
                                >
                                    <option value=""></option>
                                    @foreach($appTypes as $element)
                                        <option value="{{ $element['id_application_type'] }}">{{ $element['application_type'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- <div class="col permission d-none">
                            <div class="form-group">
                                <label>Obligación:<span class="star">*</span></label>
                                <select 
                                    id="IdObligation"
                                    name="IdObligation"
                                    class="form-control inputs-forms"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    required title="Selecciona una opción"
                                >
                                    <option value=""></option>
                                    @foreach($obligations as $element)
                                    <option value="{{ $element['id_obligation'] }}">{{ $element['title'] }}</option>
                                    @endforeach
                                    
                                </select>
                            </div>
                        </div>   -->
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
                        <!-- <div class="col-sm-12 col-md-12 col-lg-12 inputs-forms-text">
                            <div class="form-group">
                                <label>Ayuda del requerimiento</label>
                                <textarea 
                                    id="requirementHelp" 
                                    name="requirementHelp" 
                                    type="text" 
                                    class="form-control inputs-forms" 
                                    placeholder="Ayuda del requerimiento"
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div> -->
                        <!-- <div class="col-sm-12 col-md-12 col-lg-12 inputs-forms-text">
                            <div class="form-group">
                                <label>Criterio de aceptación</label>
                                <textarea 
                                    id="requirementAcceptance" 
                                    name="requirementAcceptance" 
                                    type="text" 
                                    class="form-control inputs-forms" 
                                    placeholder="Criterio de aceptación"
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div> -->
                        <!-- <div class="col-sm-12 col-md-12 col-lg-12 d-none inputs-forms-html">
                            <div class="form-group">
                                <label>Ayuda del requerimiento</label>
                                <div id="requirementHelp-html"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 d-none inputs-forms-html">
                            <div class="form-group">
                                <label>Criterio de aceptación</label>
                                <div id="requirementAcceptance-html"></div>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer footer-form-req">
                    <button type="submit" id="btnSubmitRequirement" class="btn btn-primary "></button>
                </div>      
            </form>      
        </div>
    </div>
</div>