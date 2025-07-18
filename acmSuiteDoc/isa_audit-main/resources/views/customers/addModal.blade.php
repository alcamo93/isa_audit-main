<div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleAdd" class="modal-title">Agregar Cliente</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setCustomerForm" method="POST" action="{{ asset('/customers/set') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Nombre<span class="star">*</span></label>
                                <input id="s-tradename" name="s-tradename" type="text" class="form-control" placeholder="Nombre comercial" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Razón social<span class="star">*</span></label>
                                <input id="s-trademark" name="s-trademark" type="text" class="form-control" placeholder="Nombre fiscal"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnAddCustomer" class="btn btn-primary ">Registrar</button>
                </div>      
            </form>      
        </div>
    </div>
</div>