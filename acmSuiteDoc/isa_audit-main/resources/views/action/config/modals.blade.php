<!-- Add Task -->
<div id="asignedModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" id="assignedModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignedTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="asignedForm" >
                <input type="hidden" name="s-idActionPlan" id="s-idActionPlan">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Responsable<span class="star">*</span></label>
                                <select id="s-idUser" name="s-idUser" class="form-control"
                                    title="Selecciona una opción" required>
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="form-check complex" style="padding-left: 0 !important;">
                                <label class="form-check-label">
                                    <input class="form-check-input" id="s-complex" type="checkbox">
                                    <span class="form-check-sign"></span>
                                    Deseo Agregar Tareas
                                </label>
                            </div>
                            <input type="hidden" name="isComposed" id="isComposed">
                            <hr class="complex">
                            <p class="text-justify complex"><b>Nota:</b> 
                                Cuando se seleccione el campo (<i class="fa fa-check-square-o" aria-hidden="true"></i>) habiliatará la sección tareas. 
                                Al quitar la selección del campo (<i class="fa fa-square-o" aria-hidden="true"></i>) desahibilitará la sección tareas.<br/>
                                Al cambiar el estado del campo <b>"Deseo Agregar Tareas"</b> una vez teniendo cambios y configuraciones hechos, 
                                estos se perderan al cambiar el estado del campo.
                            </p>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="submit" id="btnAssigned" class="btn btn-primary float-right">Asignar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Task -->
<div id="asignedTaskModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" id="assignedModalIntro2">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignedTaskTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="asignedTaskForm" >
                <input type="hidden" name="s-idTask" id="s-idTask">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Responsable<span class="star">*</span></label>
                                <select id="s-idUserTask" name="s-idUserTask" class="form-control"
                                    title="Selecciona una opción" required>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnAssignedTask" class="btn btn-primary float-right">Asignar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add real close date -->
<div id="asignedDateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" id="assignedModalIntro3">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignedDateTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="asignedCloseDateForm" >
                <input type="hidden" name="dateIdActionPlan" id="dateIdActionPlan">
                <div class="modal-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-sm-9 col-md-9 col-lg-9">
                            <div class="form-group">
                                <label>Fecha de cierre<span class="star">*</span></label>
                                <input id="s-closeDate" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-msg-min="La fecha indicada es menor a {0}" data-msg-max="La fecha indicada es mayor a {0}"/>
                            </div>
                        </div>
                        <div id="rowRealCloseDate" class="col-sm-9 col-md-9 col-lg-9 d-none">
                            <div class="form-group">
                                <label>Fecha de cierre real<span class="star">*</span></label>
                                <input id="s-realCloseDate" type="date" class="form-control" placeholder="dd/MM/AAAA"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-msg-min="La fecha indicada es menor a {0}" data-msg-max="La fecha indicada es mayor a {0}"/>
                            </div>
                        </div>
                        <div class="col-sm-10 col-md-10 col-lg-10 requestClass d-none">
                            <hr>
                            <p class="text-justify"><b>Nota:</b> 
                                La <b>Fecha de Cierre</b> ha expirado, solicite la autorización de la <b>Fecha de Cierre Real</b> en la
                                acción <b>Solicitar Fecha de Cierre Real</b> (<i class="fa fa-calendar-plus-o" aria-hidden="true"></i>)
                            </p>
                        </div>
                        <div class="col-sm-10 col-md-10 col-lg-10 pendientClass d-none">
                            <hr>
                            <p class="text-justify"><b>Nota:</b> 
                                Tu solicitud para la autorización de <b>Fecha de Cierre Real</b>
                                continua como <b>Pendiente</b>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnAssignedDate" class="btn btn-primary float-right">Asignar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- show requirement and subrequerimient -->
<div id="showAPModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="assignedModalIntro4">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showAPTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="showAPText" class="modal-body">
                
            </div>
        </div>
    </div>
</div>