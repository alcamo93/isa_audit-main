<!--Modal password-->
<div id="passwordModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="passModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titlePass"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpPassword();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form" id="passwordUserForm" method="POST" action="{{ asset('/users/password/set') }}">
                <input type="hidden" name="idUserPass" id="idUserPass">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Nueva Contraseña<span class="star">*</span></label>
                                <input type="password" id="newPassword" name="newPassword" placeholder="Escribe una nueva contraseña" class="form-control"
                                    data-rule-required="true" data-msg-required="Es necesario escribas tu nueva contraseña"
                                    data-rule-minlength="8" data-msg-minlength="Mínimo 8 caracteres">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Repetir Contraseña<span class="star">*</span></label>
                                <input type="password" id="repitPassword" name="repitPassword" placeholder="Repite la contraseña" class="form-control"
                                    data-rule-required="true" data-msg-required="Es necesario que repitas la contraseña"
                                    data-rule-minlength="8" data-msg-minlength="Mínimo 8 caracteres"
                                    data-rule-equalTo="#newPassword" data-msg-equalTo="Las contraseñas no coincide">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnSavePassword" type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--Modal password end-->