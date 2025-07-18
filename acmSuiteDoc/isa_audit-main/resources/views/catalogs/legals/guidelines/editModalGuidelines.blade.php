<div id="editModalGuideline" class="modal fade edit-guidelines-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="editGuidelineModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleEditGuideline" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.edit-guidelines-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="updateGuidelineForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Clasificación<span class="star">*</span></label>
                                <select 
                                    id="legal_classification_u" 
                                    name="legal_classification_u" 
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
                                <input id="initials_guideline_u" name="initials_guideline_u" type="text" class="form-control" placeholder="Inciales"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Última reforma</label>
                                <input id="lastDate_u" type="date" class="form-control" placeholder="dd/MM/AAAA"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Competencia<span class="star">*</span></label>
                                <select 
                                    id="application_type_u" 
                                    name="application_type_u" 
                                    class="form-control"
                                    onchange="handleFieldsLocations(this.value, 'u')"
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
                        <div class="col statesSelection_u d-none">
                            <div class="form-group">
                                <label>Estado<span class="star">*</span></label>
                                <select 
                                    class="form-control" 
                                    id="state_u"
                                    name="state_u"
                                    title="Selecciona una opción"
                                    data-rule-required="true"
                                    data-msg-required="Este campo es obligatorio"
                                    onchange="setCities(this.value, '#city_u')"
                                    >
                                    <option value="" selected>Selecciona una opción</option>
                                    @foreach($states as $element)
                                        <option value="{{ $element['id_state'] }}">{{ $element['state'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    <div class="col citiesSelection_u d-none">
                            <div class="form-group">
                                <label>Ciudad<span class="star">*</span></label>
                                <select 
                                    class="form-control" 
                                    id="city_u"
                                    name="city_u"
                                    title="Selecciona una opción"
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value="">Selecciona una opción</option>
                                        @foreach($cities as $element)
                                            <option value="{{ $element['id_city'] }}">{{ $element['city'] }}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Ley/Reglamento/Norma<span class="star">*</span></label>
                                <input id="guideline_u" name="guideline_u" type="text" class="form-control" placeholder="Nombre de la Ley/Reglamento/Norma" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="800" data-msg-maxlength="Máximo 800 caracteres"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnEditGuideline" href="#" class="btn btn-primary float-right">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>