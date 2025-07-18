<div id="asignedDateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" id="assignedModalIntro3">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignedDateTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="asignedDateForm" >
                <input type="hidden" name="dateIdObligation" id="dateIdObligation">
                <div class="modal-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-sm-9 col-md-9 col-lg-9">
                            <div class="form-group">
                                <label>Fecha de Expedición<span class="star">*</span></label>
                                <input id="s-initDate" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    onchange="calculateDates(this.value)"/>
                            </div>
                        </div>
                        <div class="col-sm-9 col-md-9 col-lg-9">
                            <p class="text-justify">
                                <b>Periodo de actulización: </b>
                                <span id="updatePeriodText"></span>
                            </p>
                        </div>
                        <div class="col-sm-9 col-md-9 col-lg-9">
                            <p class="text-justify">
                                <b>Fecha de Renovación: </b>
                                <span id="renewalDate"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary float-right">Asignar</button>
                </div>
            </form>
        </div>
    </div>
</div>