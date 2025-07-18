<div id="renewModal" class="modal fade renew-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="renewModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleRenew" class="modal-title">Renovar contrato</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.renew-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="renewContractForm" method="POST" action="{{ asset('/contracts/renew') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Licencia<span class="star">*</span></label>
                                <input type="hidden" id="r-idContract">
                                <select id="r-idLicense" name="r-idLicense" class="form-control"  style="width: 100%;"
                                        onchange="getDetailsLicenses(this.value, '#licensesRenewDataTable', '#r-dateStart', '#r-dateEnd')"
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
                                <input disabled id="r-dateStart" type="date" class="form-control" 
                                    onchange="calculateDateEnd(event, '#r-dateEnd', '#r-idLicense')" placeholder="dd/MM/AAAA"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-msg-min="La fecha no puede ser anterior a la actual"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label>Fecha de fin<span class="star">*</span></label>
                                <input disabled id="r-dateEnd" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive"> 
                                <table id="licensesRenewDataTable" class="table table-striped table-hover" cellspacing="0" width="100%">
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
                    <button class="btn btn-primary float-right btnContractExtension" f="#renewContractForm" et="1" m="#renewModal">Renovar</button>
                </div>      
            </form>      
        </div>
    </div>
</div>