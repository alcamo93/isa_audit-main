<div id="addModalGuideline" class="modal fade guidelines-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addGuidelineModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Ley/Reglamento/Norma</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.guidelines-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setGuidelineForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Clasificación<span class="star">*</span></label>
                                <select 
                                    id="legal_classification_s" 
                                    name="legal_classification_s" 
                                    class="form-control"
                                    data-rule-required="true"
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value=""></option>
                                    @foreach($legalC as $element)
                                        <option value="{{ $element['id_legal_c'] }}">{{ $element['legal_classification'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-8 col-lg-8">
                            <div class="form-group">
                                <label>Siglas<span class="star">*</span></label>
                                <input id="initials_guideline_s" name="initials_guideline_s" type="text" class="form-control" placeholder="Inciales"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Última reforma</label>
                                <input id="lastDate_s" type="date" class="form-control" placeholder="dd/MM/AAAA"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Competencia<span class="star">*</span></label>
                                <select 
                                    id="application_type_s" 
                                    name="application_type_s" 
                                    class="form-control"
                                    onchange="handleFieldsLocations(this.value, 's')"
                                    data-rule-required="true"
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value=""></option>
                                    @foreach($appTypes as $element)
                                        <option value="{{ $element['id_application_type'] }}">{{ $element['application_type'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col statesSelection_s d-none">
                            <div class="form-group">
                                <label>Estado<span class="star">*</span></label>
                                <select 
                                    class="form-control" 
                                    id="state_s"
                                    name="state_s"
                                    title="Selecciona una opción"
                                    data-msg-required="Este campo es obligatorio"
                                    onchange="setCities(this.value, '#city_s')"
                                    >
                                    <option value="">Selecciona una opción</option>
                                        @foreach($states as $element)
                                            <option value="{{ $element['id_state'] }}">{{ $element['state'] }}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col citiesSelection_s d-none">
                            <div class="form-group">
                                <label>Ciudad<span class="star">*</span></label>
                                <select 
                                    class="form-control" 
                                    id="city_s"
                                    name="city_s"
                                    title="Selecciona una opción"
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Ley/Reglamento/Norma<span class="star">*</span></label>
                                <input id="guideline_s" name="guideline_s" type="text" class="form-control" placeholder="Nombre de la Ley/Reglamento/Norma" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="800" data-msg-maxlength="Máximo 800 caracteres"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnAddGuideline" href="#" class="btn btn-primary float-right">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>