<div id="subrequirementModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" id="addModalIntro">
        <div class="modal-content subrequirement-Modal">
            <div class="modal-header">
                <h5 id="subrequirementModalTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('subrequirement-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="subrequirementForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-3">
                            <div class="form-group">
                                <label>Condición<span class="star">*</span></label>
                                <select
                                    id="IdCondition-sr"
                                    name="IdCondition-sr"
                                    class="form-control"
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
                        <div class="col-sm-12 col-md-12 col-lg-3">
                            <div class="form-group">
                                <label>Periodo de cierre<span class="star">*</span></label>
                                <select
                                    id="IdObtainingPeriod-sr"
                                    name="IdObtainingPeriod-sr"
                                    class="form-control"
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
                        <div class="col-sm-12 col-md-12 col-lg-3">
                            <div class="form-group">
                                <label>Periodo de actualización</label>
                                <select
                                    id="IdUpdatePeriod-sr"
                                    name="IdUpdatePeriod-sr"
                                    class="form-control"
                                    >
                                    <option value="">No aplica</option>
                                    @foreach($periods as $element)
                                        <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-3">
                           <div class="form-group">
                                <label>Orden<span class="star">*</span></label>
                                <input 
                                    id="orderSub" 
                                    name="orderSub" 
                                    type="number" 
                                    class="form-control" 
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
                                <label>Evidencia<span class="star">*</span></label>
                                <select 
                                    id="idEvidence-sr"
                                    name="idEvidence-sr"
                                    class="form-control"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    onclick="setSpecificDocument(this.value, '#subDocument')"
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
                                    id="subDocument" 
                                    name="subDocument"
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Nombre del documento" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="400" 
                                    data-msg-maxlength="Máximo 400 caracteres"
                                />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Tipo de subrequerimiento<span class="star">*</span></label>
                                <select 
                                    id="IdRequirementType-sr" 
                                    name="filterIdRequirementType-sr" 
                                    class="form-control"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>N° de subrequerimiento<span class="star">*</span></label>
                                <input 
                                    id="noSubrequirement" 
                                    name="noSubrequirement" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Numero de subrequerimiento" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Subrequerimiento<span class="star">*</span></label>
                                <textarea 
                                    id="subrequirement" 
                                    name="subrequirement" 
                                    type="text" 
                                    class="form-control" 
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
                                    id="subrequirementDesc" 
                                    name="subrequirementDesc" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Descripción" 
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Ayuda del subrequerimiento</label>
                                <textarea 
                                    id="subrequirementHelp" 
                                    name="subrequirementHelp" 
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
                                    id="subrequirementAcceptance" 
                                    name="subrequirementAcceptance" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Criterio de aceptación"
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSubmitSubrequirement" class="btn btn-primary "></button>
                </div>      
            </form>      
        </div>
    </div>
</div>