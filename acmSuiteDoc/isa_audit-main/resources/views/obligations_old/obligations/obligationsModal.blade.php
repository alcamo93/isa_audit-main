<div id="obligationsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="obligationsModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="obligationsModalTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="obligationsForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Periodo<span class="star">*</span></label>
                                <select
                                    id="idPeriod"
                                    name="idPeriod" 
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
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Condición<span class="star">*</span></label>
                                <select
                                    id="idCondition"
                                    name="idCondition" 
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
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Tipo<span class="star">*</span></label>
                                <select
                                    id="idObType"
                                    name="idObType" 
                                    class="form-control"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value=""></option>
                                    @foreach($obligationTypes as $element)
                                        <option value="{{ $element['id_obligation_type'] }}">{{ $element['obligation_type'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Título<span class="star">*</span></label>
                                <input 
                                    id="title" 
                                    name="title" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Título"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Obligación<span class="star">*</span></label>
                                <textarea 
                                    id="obligation" 
                                    name="obligation" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Obligación"
                                    rows="3"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSubmitObligation" class="btn btn-primary "></button>
                </div>      
            </form>      
        </div>
    </div>
</div>

<!-- Add real close date -->
<div id="asignedDateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" id="assignedModalIntro3">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignedDateTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="asignedDateForm" >
                <input type="hidden" name="dateIdObligation" id="dateIdObligation">
                <div class="modal-body">
                    <div class="row d-flex justify-content-center">
                        <input type="hidden" name="idPeriodCalc" id="idPeriodCalc">
                        <div class="col-sm-9 col-md-9 col-lg-9">
                            <div class="form-group">
                                <label>Fecha de Expedición<span class="star">*</span></label>
                                <input id="s-initDate" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    onchange="calculateDates(this.value)"/>
                            </div>
                        </div>
                        <div class="col-sm-9 col-md-9 col-lg-9">
                            <p class="text-justify">
                                <b>Actualización cada: </b>
                                <span id="updatePeriodText"></span>
                            </p>
                        </div>
                        <div class="col-sm-9 col-md-9 col-lg-9">
                            <div class="form-group">
                                <label>Fecha de Renovación<span class="star">*</span></label>
                                <input id="s-renewalDate" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-msg-min="La fecha indicada es menor a {0}" data-msg-max="La fecha indicada es mayor a {0}"/>
                            </div>
                        </div>
                        <div id="rowRealCloseDate" class="col-sm-9 col-md-9 col-lg-9 d-none">
                            <div class="form-group">
                                <label>Fecha de Renovación Real<span class="star">*</span></label>
                                <input id="s-lastRenewalDate" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-msg-min="La fecha indicada es menor a {0}" data-msg-max="La fecha indicada es mayor a {0}"/>
                            </div>
                        </div>
                        <div class="col-sm-10 col-md-10 col-lg-10 requestClass d-none">
                            <hr>
                            <p class="text-justify"><b>Nota:</b> 
                                La <b>Fecha de Renovación</b> ha expirado, solicite la autorización de la <b>Fecha de Renovación Real</b> en la
                                acción <b>Solicitar Fecha de Renovación Real</b> (<i class="fa fa-calendar-plus-o" aria-hidden="true"></i>)
                            </p>
                        </div>
                        <div class="col-sm-10 col-md-10 col-lg-10 pendientClass d-none">
                            <hr>
                            <p class="text-justify"><b>Nota:</b> 
                                Tu solicitud para la autorización de <b>Fecha de Renovación Real</b>
                                continua como <b>Pendiente</b>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnAssignedDate" class="btn btn-primary float-right">Asignar</button>
                </div>
            </form>
        </div>
    </div>
</div>