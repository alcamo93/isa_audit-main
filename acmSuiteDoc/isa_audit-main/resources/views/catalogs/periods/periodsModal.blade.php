<div id="periodsModal" class="modal fade periods-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.periods-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="periodsForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Nombre<span class="star">*</span></label>
                                <input id="name" name="name" type="text" class="form-control" placeholder="Periodo" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col"> Tiempo para la fecha de término (expectativa)</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Días</label>
                                <select id="days" name="days" type="text" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Meses</label>
                                <select id="months" name="months" type="text" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Años</label>
                                <select id="years" name="years" type="text" class="form-control">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col"> Tiempo para la fecha de término real </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Días</label>
                                <select id="days-r" name="days-r" type="text" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Meses</label>
                                <select id="months-r" name="months-r" type="text" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Años</label>
                                <select id="years-r" name="years-r" type="text" class="form-control">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnModalPeriods" class="btn btn-primary "></button>
                </div>      
            </form>      
        </div>
    </div>
</div>