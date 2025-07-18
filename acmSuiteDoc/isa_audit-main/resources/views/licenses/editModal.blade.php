<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="editModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleEdit" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.editModal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="updateLicenseForm" method="POST" action="{{ asset('/licenses/update') }}">
                <input type="hidden" id="idLicense" name="idLicense">
                <div class="modal-body">
                    
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Licencia<span class="star">*</span></label>
                                <input id="u-license" name="u-license" type="text" class="form-control" placeholder="Identificación de licencia" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="50" data-msg-maxlength="Máximo 50 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label># Globales<span class="star">*</span></label>
                                <input id="u-usrGlobals" name="u-usrGlobals" type="number" class="form-control" placeholder="Especificar número"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-digits="true" data-msg-digits="Solo se permiten dígitos positivos"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label># Corporativos<span class="star">*</span></label>
                                <input id="u-usrCorporates" name="u-usrCorporates" type="number" class="form-control" placeholder="Especificar número"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-digits="true" data-msg-digits="Solo se permiten dígitos positivos"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label># Operativos<span class="star">*</span></label>
                                <input id="u-usrOperatives" name="u-usrOperatives" type="number" class="form-control" placeholder="Especificar número"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-digits="true" data-msg-digits="Solo se permiten dígitos positivos"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="u-idStatus" name="u-idStatus" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Periodo<span class="star">*</span></label>
                                <select id="u-idPeriod" name="u-idPeriod" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                    @foreach($periods as $element)
                                        <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                                    @endforeach
                                </select>
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