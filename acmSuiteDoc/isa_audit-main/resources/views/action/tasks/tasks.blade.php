<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="form-group text-center">
                            <label id="noReqTitleTaskLabel" class="font-weight-bold"></label>
                            <div id="noReqTitleTask"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group text-center">
                            <label id="reqTitleTaskLabel" class="font-weight-bold"></label>
                            <div id="reqTitleTask"></div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="idActionPlan" id="idActionPlan">
                <input type="hidden" name="idPeriodCurrent" id="idPeriodCurrent">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <!-- <button type="button" class="close my-close" data-toggle="tooltip" 
                            title="Ayuda" onclick="helpAction();">
                            <i class="fa fa-question-circle-o"></i>
                        </button> -->
                    </div>
                    <div class="col">
                        <button type="button" id="buttonAddTask" class="btn btn-success float-right" data-toggle="tooltip" 
                            title="Agregar tarea" onclick="addTask();">
                            Agregar
                        </button>
                        <button type="button" class="btn btn-success float-right mr-2" data-toggle="tooltip" 
                            title="Regresar" onclick="closeTasks();">
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tasksTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Identificador</th>
                                <th class="text-center">Tarea</th>
                                <th class="text-center">Responsable</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Periodo</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>