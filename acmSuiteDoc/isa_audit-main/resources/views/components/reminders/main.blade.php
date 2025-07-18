<!-- Add reminders close date-->
<div id="remindersCloseModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remindersCloseTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="remindersCloseForm" >
                <div class="modal-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <div class="form-group">
                                <label>Fechas de notificación (<b id="textClose"></b>)</label>
                                <input id="s-reminder" type="text" class="form-control d-none">
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row d-flex justify-content-center">
                        <div class="col-sm-10 col-md-10 col-lg-10">
                            <hr>
                            <p class="text-justify">
                                Selecciona las fechas en las que quieras que el sistema te recuerde tu pendientes
                                antes de que expire la <b id="textCloseFooter"></b>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add reminders real close date-->
<div id="remindersRealCloseModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remindersRealCloseTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="remindersRealCloseForm" >
                <div class="modal-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <div class="form-group">
                                <label>Fechas de notificación (<b id="textRealClose"></b>)</label>
                                <input id="s-reminderReal" type="text" class="form-control d-none">
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row d-flex justify-content-center">
                        <div class="col-sm-10 col-md-10 col-lg-10">
                            <hr>
                            <p class="text-justify">
                                Selecciona las fechas en las que quieras que el sistema te recuerde tu pendientes
                                antes de que expire la <b id="textRealCloseFooter"></b>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>