<div id="fullViewModal" class="modal fade full-view-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="fullViewModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleEdit" class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Contrato</label>
                            <div id="u-contract"></div>
                            <input type="hidden" id="idContract" name="idContract">
                            <input type="hidden" id="idContractDetail" name="idContractDetail">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Cliente</label>
                            <div id="u-idCustomer" ></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Planta</label>
                            <div id="u-idCorporate" ></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Licencia</label>
                            <div id="u-idLicense" ></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Fecha de inicio</label>
                            <div id="u-dateStart" ></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Fecha de fin</label>
                            <div id="u-dateEnd" ></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Periodo</label>
                            <div id="u-idPeriod-text"></div>
                            <select id="u-idPeriod" name="u-idPeriod" class="form-control d-none"
                                    title="Selecciona una opción" required
                                    onchange="calculateUpdate()">
                                <option value=""></option>
                                @foreach($periods as $element)
                                    <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label class="font-weight-bold"># Globales</label>
                            <div id="u-usrGlobals"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label class="font-weight-bold"># Corporativos</label>
                            <div id="u-usrCorporates" ></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label class="font-weight-bold"># Operativos</label>
                            <div id="u-usrOperatives" ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

