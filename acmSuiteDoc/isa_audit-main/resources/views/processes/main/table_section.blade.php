<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <button 
                            type="button"
                            class="close my-close"
                            data-toggle="tooltip" 
                            title="Ayuda"
                            onclick="helpMain('.toggle-processes', '#buttonAddProcesses', '#actionsProcesses')"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right"
                                onclick="openAddProcesses()" id="buttonAddProcesses">
                            Agregar <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="processesTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Auditoría</th>
                                <th>Planta</th>
                                <th>Alcance</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center">Secciones</th>
                                <th class="text-center" id="actionsProcesses">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('components.loading')
</div>