<div id="contactModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" id="contactModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="contactTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpContact();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form" id="contactForm" method="POST" action="{{ asset('/corporates/contact/set') }}">
                    <input type="hidden" id="idCorpCt" name="idCorpCt">
                    <input type="hidden" id="idContact" name="idContact">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Correo eléctronico<span class="star">*</span></label>
                                <input id="ctEmail" name="ctEmail" type="text" class="form-control" placeholder="ejemplo@mail.com" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-email="true" data-msg-email="El formato de correo es incorrecto"
                                    data-rule-maxlength="45" data-msg-maxlength="Máximo 45 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Nombre(s)<span class="star">*</span></label>
                                <input id="ctFirstName" name="ctFirstName" type="text" class="form-control" placeholder="Nombre(s)"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="255" data-msg-maxlength="Máximo 255 caracteres"/>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Primer Apellido<span class="star">*</span></label>
                                <input id="ctSecondName" name="ctSecondName" type="text" class="form-control" placeholder="Apellido paterno"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="255" data-msg-maxlength="Máximo 255 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Segundo Apellido</label>
                                <input id="ctLastName" name="ctLastName" type="text" class="form-control" placeholder="Apellido Materno"
                                    data-rule-maxlength="255" data-msg-maxlength="Máximo 255 caracteres"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Puesto<span class="star">*</span></label>
                                <input id="ctDegree" name="ctDegree" type="text" class="form-control" placeholder="Director general" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="45" data-msg-maxlength="Máximo 45 caracteres"/>
                            </div>
                        </div> 
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label>Celular<span class="star">*</span></label>
                                <input id="ctCell" name="ctCell" type="text" class="form-control" placeholder="00 0000 0000"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-number="true" data-msg-number="Solo se permiten números"
                                    data-rule-maxlength="16" data-msg-maxlength="Máximo 16 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-2">
                            <div class="form-group">
                                <label>Extensión</label>
                                <input id="ctPhExtOffice" name="ctPhExtOffice" type="text" class="form-control" placeholder="00 0000 0000"
                                    data-rule-number="true" data-msg-number="Solo se permiten números"
                                    data-rule-maxlength="16" data-msg-maxlength="Máximo 16 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-3">
                            <div class="form-group">
                                <label>Teléfono oficina</label>
                                <input id="ctPhOffice" name="ctPhOffice" type="text" class="form-control" placeholder="00 0000 0000"
                                    data-rule-number="true" data-msg-number="Solo se permiten números"
                                    data-rule-maxlength="16" data-msg-maxlength="Máximo 16 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer btn-group float-right" role="group">
                        <button type="button" id="btnDeleteContact" class="btn btn-danger float-right element-hide">Eliminar</button>
                        <button type="submit" id="btnAddContact" class="btn btn-primary float-right">Guardar</button>
                    </div>
                </form>
            </div>   
        </div>
    </div>
</div>