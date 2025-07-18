<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <button type="button" id="buttonAddTask" class="btn btn-success float-right" data-toggle="tooltip" 
                        title="Agregar tarea" onclick="addTask();">
                        Agregar Tarea
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
                            <th class="text-center">Responsable de tarea</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Fecha Inicio</th>
                            <th class="text-center">Fecha Cierre</th>
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