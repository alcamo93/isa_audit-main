<form id="actionFilterForm">
    @include('action_w.requirements.action_filters')
</form>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <button type="button" class="close my-close" data-toggle="tooltip" 
                            title="Ayuda" onclick="helpMain('.toggle-matter-aspect', null, '#actions', '#comebackContracts');">
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button
                            type="button"
                            id="comebackContracts"
                            class="btn btn-success float-right"
                            data-toggle="tooltip" 
                            title="Regresar" onclick="closeAction();"
                            >
                            Regresar
                        </button>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div>
                        <div class="btn-group" role="group">
                            <button type="button"
                                onclick="setStatus(13)"
                                class="btn btn-secondary">
                                <span id="count_unassigned" class="badge">0</span>
                                Sin asignar
                            </button>
                            <button type="button"
                                onclick="setStatus(17)"
                                class="btn btn-success">
                                <span id="count_complete" class="badge">0</span>
                                Completado
                            </button>
                            <button type="button"
                                onclick="setStatus(25)"
                                class="btn btn-danger">
                                <span id="count_expired" class="badge">0</span>
                                Vencido
                            </button>
                            <button type="button"
                                onclick="setStatus(16)"
                                class="btn btn-warning">
                                <span id="count_progress" class="badge">0</span>
                                En Curso
                            </button>
                            <button type="button"
                                onclick="setStatus(18)"
                                class="btn btn-primary">
                                <span id="count_review" class="badge">0</span>
                                En Revisión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="actionTable" class="table table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="text-center">Requerimiento</th>
                                <th class="text-center">Nivel de Riesgo</th>
                                <th class="text-center">Prioridad</th>
                                <th class="text-center">Hallazgo</th>
                                <th class="text-center">Fecha Inicio</th>
                                <th class="text-center">Fecha Cierre</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center">Responsable de Cierre</th>
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
@include('action_w.requirements.expired_table')