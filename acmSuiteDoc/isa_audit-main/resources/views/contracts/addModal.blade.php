<div id="addModal" class="modal fade add-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleAdd" class="modal-title">Agregar Contrato</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setContractForm" method="POST" action="{{ asset('/contracts/set') }}">
                <input type="hidden" id="idContract">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Contrato<span class="star">*</span></label>
                                <input id="s-contract" name="s-contract" type="text" class="form-control" placeholder="Identificación de licencia" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="50" data-msg-maxlength="Máximo 50 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="row not-update-data">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Cliente<span class="star">*</span></label>
                                <select id="s-idCustomer" name="s-idCustomer" class="form-control"
                                        onchange="setCorporates(this.value, '#s-idCorporate')"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                    @foreach($customers as $element)
                                        <option value="{{ $element['id_customer'] }}">{{ $element['cust_trademark'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Planta<span class="star">*</span></label>
                                <select id="s-idCorporate" name="s-idCorporate" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none update-data">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Cliente</label>
                                <div id="customerName"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Planta</label>
                                <div id="corporateName"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row not-update-data">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Licencia<span class="star">*</span></label>
                                <select id="s-idLicense" name="s-idLicense" class="form-control"  style="width: 100%;"
                                        onchange="getDetailsLicenses(this.value, '#licensesDataTable', '#s-dateStart', '#s-dateEnd')"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                    @foreach($licenses as $element)
                                        <option value="{{ $element['id_license'] }}">{{ $element['license'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label>Fecha de inicio<span class="star">*</span></label>
                                <input disabled id="s-dateStart" type="date" class="form-control" 
                                    onchange="calculateDateEnd(event, '#s-dateEnd', '#s-idLicense')" placeholder="dd/MM/AAAA"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-msg-min="La fecha no puede ser anterior a la actual"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label>Fecha de fin<span class="star">*</span></label>
                                <input disabled id="s-dateEnd" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive"> 
                                <table id="licensesDataTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nivel Global</th>
                                            <th class="text-center">Nivel Corporativo</th>
                                            <th class="text-center">Nivel Operativo</th>
                                            <th class="text-center">Durabilidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="text-center" colspan="4">Selecciona una licencia</i></th>
                                        </tr>
                                    </tbody>
                                </table>
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
