<!-- Add Task -->
<div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo tarea</h5>
                <!-- <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span> -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setTask" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Identificador<span class="star">*</span></label>
                                <input id="s-title" name="s-title" type="text" class="form-control" placeholder="Identificación de tarea" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="50" data-msg-maxlength="Máximo 50 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Periodo<span class="star">*</span></label>
                                <select id="s-idPeriod" name="s-idPeriod" class="form-control"
                                    title="Selecciona una opción" required>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row alert-period d-none">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <p class="text-justify"><b>Nota:</b> 
                                La suma de periodos de las tareas es igual al perido de este requerimiento
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Tarea<span class="star">*</span></label>
                                <textarea 
                                    id="s-task" 
                                    name="s-task" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Tarea" 
                                    rows="3"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    ></textarea>
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
<!-- Edit Task -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="editModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalTask"></h5>
                <!-- <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span> -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="editTask" >
                <input type="hidden" name="idTask" id="idTask">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Identificador<span class="star">*</span></label>
                                <input id="u-title" name="u-title" type="text" class="form-control" placeholder="Identificación de tarea" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="50" data-msg-maxlength="Máximo 50 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Periodo<span class="star">*</span></label>
                                <select id="u-idPeriod" name="u-idPeriod" class="form-control"
                                    title="Selecciona una opción" required>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row alert-period d-none">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <p class="text-justify"><b>Nota:</b> 
                                La suma de periodos de las tareas es igual al perido de este requerimiento
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Tarea<span class="star">*</span></label>
                                <textarea 
                                    id="u-task" 
                                    name="u-task" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Tarea" 
                                    rows="3"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnUpdate" class="btn btn-primary float-right">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Asigned User in task -->