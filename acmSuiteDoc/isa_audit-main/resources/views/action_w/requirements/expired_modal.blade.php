<!-- Expired requirement -->
<div id="expiredModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="assignedModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expiredTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="expiredForm" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Requerimiento<span class="star">*</span></label>
                                <div class="label_edit form-control h-auto" id="requirementName"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Causa de desviación<span class="star">*</span></label>
                                <textarea 
                                    id="s-cause" 
                                    name="s-cause" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Especifica el criterio" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Fecha de resolución<span class="star">*</span></label>
                                <input id="s-real_close_date" type="date" 
                                    class="form-control" placeholder="dd/mm/aaaa"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    autocomplete="off" 
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary float-right">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Expired requirement stage two-->
<div id="expiredAgainModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expiredAgainTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="expiredAgainForm" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Requerimiento<span class="star">*</span></label>
                                <div class="label_edit form-control h-auto" id="s2-requirementName"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Causa de desviación<span class="star">*</span></label>
                                <textarea 
                                    id="s2-cause" 
                                    name="s2-cause" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Especifica el criterio" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Fecha de resolución<span class="star">*</span></label>
                                <input id="s2-real_close_date" name="s2-real_close_date" type="date" 
                                    class="form-control" placeholder="dd/mm/aaaa"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    autocomplete="off" 
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary float-right">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>