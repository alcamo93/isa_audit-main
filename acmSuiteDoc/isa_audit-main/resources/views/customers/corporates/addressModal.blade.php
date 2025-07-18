<div id="addressModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" id="addressModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="addressTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAddress();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body physical address-form">
                <form class="form" id="addressForm" method="POST" action="{{ asset('/corporates/address/set') }}">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <h5><b>Direccion Física</b></h5>
                        </div>
                    </div>

                    <input type="hidden" id="idCorpAddressPh" name="idCorpAddressPh">
                    <input type="hidden" id="idAddressPh" name="idAddressPh">
                    <input type="hidden" id="typeAddressPh" name="typeAddress" value="1">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Calle<span class="star">*</span></label>
                                <input id="streetPh" name="streetPh" type="text" class="form-control" placeholder="Nombre de la calle" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="150" data-msg-maxlength="Máximo 150 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Colonia<span class="star">*</span></label>
                                <input id="suburbPh" name="suburbPh" type="text" class="form-control" placeholder="Nombre de la colonia"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="50" data-msg-maxlength="Máximo 50 caracteres"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Número exterior</label>
                                <input id="numExtPh" name="numExtPh" type="text" class="form-control" placeholder="#50"
                                    data-rule-maxlength="60" data-msg-maxlength="Máximo 60 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Número interior</label>
                                <input id="numIntPh" name="numIntPh" type="text" class="form-control" placeholder="#13"
                                    data-rule-maxlength="60" data-msg-maxlength="Máximo 60 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Código Postal</label>
                                <input id="zipPh" name="zipPh" type="text" class="form-control" placeholder="12340"
                                    data-rule-minlength="5" data-msg-minlength="Mínimo 5 caracteres"
                                    data-rule-maxlength="10" data-msg-maxlength="Máximo 10 caracteres"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>País<span class="star">*</span></label>
                                <select class="form-control" id="idCountryPh" name="idCountryPh" 
                                        onchange="setStates(this.value, '#idStatePh', '#idCityPh')"
                                        required title="Selecciona una opción">
                                    <option value=""></option>
                                    @foreach($countries as $element)
                                        <option value="{{ $element['id_country'] }}">{{ $element['country'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Estado<span class="star">*</span></label>
                                <select class="form-control" id="idStatePh" name="idStatePh" 
                                        onchange="setCities(this.value, '#idCityPh')"
                                        required title="Selecciona una opción">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Ciudad<span class="star">*</span></label>
                                <select class="form-control" id="idCityPh" name="idCityPh"
                                        required title="Selecciona una opción">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="btn-group float-right" role="group">
                                <button type="button" id="btnDeleteAddressPh" class="btn btn-danger float-right element-hide mr-2">Eliminar</button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-2">
                            <h5 class="pt-2"><b>Direccion Fiscal</b></h5>
                        </div>
                        <div class="col-sm-8 col-md-8 col-lg-10">
                            <button type="button" id="btnCopyAddressPh" class="btn btn-info mr-2">Copiar dirección física</button>
                        </div>
                    </div>

                    <input type="hidden" id="idCorpAddressFi" name="idCorpAddressFi">
                    <input type="hidden" id="idAddressFi" name="idAddressFi">
                    <input type="hidden" id="typeAddressFi" name="typeAddressFi" value="0">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Calle<span class="star">*</span></label>
                                <input id="streetFi" name="streetFi" type="text" class="form-control" placeholder="Nombre de la calle"
                                       data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                       data-rule-maxlength="150" data-msg-maxlength="Máximo 150 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Colonia<span class="star">*</span></label>
                                <input id="suburbFi" name="suburbFi" type="text" class="form-control" placeholder="Nombre de la colonia"
                                       data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                       data-rule-maxlength="50" data-msg-maxlength="Máximo 50 caracteres"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Número exterior</label>
                                <input id="numExtFi" name="numExtFi" type="text" class="form-control" placeholder="#50"
                                       data-rule-maxlength="60" data-msg-maxlength="Máximo 60 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Número interior</label>
                                <input id="numIntFi" name="numIntFi" type="text" class="form-control" placeholder="#13"
                                       data-rule-maxlength="60" data-msg-maxlength="Máximo 60 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Código Postal</label>
                                <input id="zipFi" name="zipFi" type="text" class="form-control" placeholder="12340"
                                       data-rule-minlength="5" data-msg-minlength="Mínimo 5 caracteres"
                                       data-rule-maxlength="10" data-msg-maxlength="Máximo 10 caracteres"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>País<span class="star">*</span></label>
                                <select class="form-control" id="idCountryFi" name="idCountryFi"
                                        onchange="setStates(this.value, '#idStateFi', '#idCityFi')"
                                        required title="Selecciona una opción">
                                    <option value=""></option>
                                    @foreach($countries as $element)
                                        <option value="{{ $element['id_country'] }}">{{ $element['country'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Estado<span class="star">*</span></label>
                                <select class="form-control" id="idStateFi" name="idStateFi"
                                        onchange="setCities(this.value, '#idCityFi')"
                                        required title="Selecciona una opción">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Ciudad<span class="star">*</span></label>
                                <select class="form-control" id="idCityFi" name="idCityFi"
                                        required title="Selecciona una opción">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="btn-group float-right" role="group">
                                <button type="button" id="btnDeleteAddressFi" class="btn btn-danger float-right element-hide mr-2">Eliminar</button>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 text-center">
                            <div class="btn-group" role="group">
                                <button type="submit" id="btnAddAddress" class="btn btn-primary center mr-2">Guardar direcciones</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
