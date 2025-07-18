<!-- Add Task -->
<div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalTask"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setTask" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Identificador<span class="star">*</span></label>
                                <input id="s-title" name="s-title" type="text" class="form-control input-edit acm-task-control" 
                                    placeholder="Identificación de tarea" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="50" data-msg-maxlength="Máximo 50 caracteres"/>
                                <div class="label_edit form-control h-auto d-none" id="u-title" name="u-title"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label for="s-initDate">Fecha de inicio<span class="star">*</span></label>
                                <input id="s-initDate" name="s-initDate" type="date" 
                                    class="form-control input-edit acm-task-control" placeholder="dd/mm/aaaa"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    autocomplete="off" 
                                />
                                <div class="label_edit form-control h-auto d-none" id="u-initDate"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label for="s-endDate">Fecha de cierre<span class="star">*</span></label>
                                <input id="s-endDate" name="s-endDate" type="date"
                                    class="form-control acm-task-control" placeholder="dd/mm/aaaa"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-msg-min="La fecha debe ser mayor a la fecha inicial"
                                    autocomplete="off" 
                                />
                            </div>
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
                                    class="form-control input-edit acm-task-control" 
                                    placeholder="Tarea" 
                                    rows="3"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    ></textarea>
                                <div class="label_edit form-control h-auto d-none" id="u-task" name="u-task"></div>
                            </div>
                        </div>
                    </div>
                    @php 
                        $selectors = array( 
                            'idUser' => 'idUser-task', 
                            'days' => 'days-task',
                            'function' => 'removeUserTask',
                            'object' => 'userAssignedTask',
                            'typeTitle' => 'tarea',
                            'idBtn' => 'btnTrashTask',
                            'classDisabled' => 'acm-task-control'); 
                    @endphp
                    @include('action_w.components.usersAssigned', $selectors)
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-danger float-right">Cancelar</button>
                    <button type="submit" id="btnRegister" class="btn btn-primary float-right">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Task -->
<div id="showModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="showModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleShowModalTask"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setTask" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Identificador</label>
                                <div class="label_edit form-control h-auto" id="w-title" name="w-title"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Fecha de inicio</label>
                                <div class="label_edit form-control h-auto" id="w-initDate" name="w-initDate"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Fecha de cierre</label>
                                <div class="label_edit form-control h-auto" id="w-endDate" name="w-endDate"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Tarea</label>
                                <div class="label_edit form-control h-auto" id="w-task" name="w-task"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class=" col-md-12 col-lg-12">
                            <div class="form-group">
                                <table id="tableUserTask" class="table table-striped table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr class="table-info">
                                            <th class="text-center font-weight-bold text-uppercase" colspan="3">
                                                Responsable de tarea
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Tipo</th>
                                            <th class="text-center">Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="sectionDownload" class="row">
                        <div class="col-sm-12">
                            <div class="btn-group btn-group-lg w-100" role="group">
                                <button type="button" id="downloadFileShow" class="btn btn-info">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                    Descargar archivo
                                </button>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-danger float-right">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>