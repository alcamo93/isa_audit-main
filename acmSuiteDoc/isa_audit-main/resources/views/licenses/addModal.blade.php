<div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleAdd" class="modal-title">Agregar Lincencia</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.addModal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setLicenseForm" method="POST" action="{{ asset('/licenses/set') }}">
                <div class="modal-body">
                    
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Licencia<span class="star">*</span></label>
                                <input id="s-license" name="s-license" type="text" class="form-control" placeholder="Identificación de licencia" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="50" data-msg-maxlength="Máximo 50 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label># Globales<span class="star">*</span></label>
                                <input id="s-usrGlobals" name="s-usrGlobals" type="number" class="form-control" placeholder="Especificar número"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-digits="true" data-msg-digits="Solo se permiten dígitos positivos"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label># Corporativos<span class="star">*</span></label>
                                <input id="s-usrCorporates" name="s-usrCorporates" type="number" class="form-control" placeholder="Especificar número"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-digits="true" data-msg-digits="Solo se permiten dígitos positivos"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label># Operativos<span class="star">*</span></label>
                                <input id="s-usrOperatives" name="s-usrOperatives" type="number" class="form-control" placeholder="Especificar número"
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
                                <select id="s-idStatus" name="s-idStatus" class="form-control"
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
                                <select id="s-idPeriod" name="s-idPeriod" class="form-control"
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
                    <button type="submit" id="btnRegister" class="btn btn-primary float-right">Registrar</button>
                </div>      
            </form>      
        </div>
    </div>
</div>