<!-- Show aspects by matter -->
<div class="matters-list-card d-none">
    <div class="row">
        <div class="col">
            <?php 
                $text = array( 
                    'title' => 'Área de filtrado', 
                    'tooltip' => ' area de filtrado', 
                    'idElement'=> '#filsterEvaluation', 
                    'idToggle' => 'evaluation' ); 
            ?>
            @include('components.toggle.toggle', $text)
            <div class="card" id="filsterEvaluation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Cliente</label>
                                <div id="customerTitle"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Planta</label>
                                <div id="corporateTitle"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre de evaluación</label>
                                <div class="auditTitle"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Alcance</label>
                                <div class="scopeAudit"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Materia</label>
                                <select id="idContractMatter" name="idContractMatter" class="form-control"
                                        onchange="reloadaspectsRegister()">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Estatus</label>
                                <select id="filterIdStatusAspect" name="filterIdStatusAspect" class="form-control"
                                        onchange="reloadaspectsRegister()">
                                    <option value="0">Todos</option>
                                        @foreach($status as $element)
                                            <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                    </div>    
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
                            <button type="button" class="close my-close" data-toggle="tooltip" 
                                title="Ayuda" onclick="helpMain('.toggle-evaluation', null, '#actionEvaluation', '#closeEvaluation');">
                                <i class="fa fa-question-circle-o"></i>
                            </button>
                        </div>
                        <div class="col">
                            @if ($idProfileType != 4 || $idProfileType != 5)
                            <button 
                                type="button"
                                class="btn btn-success float-right ml-2"
                                data-toggle="tooltip" 
                                id="closeEvaluation"
                                title="Regresar" onclick="closeAplicabilityMatters();">
                                Regresar
                            </button>
                            @endif
                            <button 
                                type="button"
                                class="btn btn-success float-right ml-2"
                                data-toggle="tooltip" 
                                id="reportAplicability"
                                title="Respuestas" onclick="reportAplicability();">
                                <i class="fa fa-file-excel-o fa-lg"></i>
                                Respuestas
                            </button>
                            <button 
                                type="button"
                                class="btn btn-success float-right ml-2"
                                data-toggle="tooltip" 
                                id="reportAplicability"
                                title="Resultados" onclick="reportResultsAplicability();">
                                <i class="fa fa-file-excel-o fa-lg"></i>
                                Resultados
                            </button>
                            <button 
                                type="button"
                                class="d-none btn btn-success float-right"
                                data-toggle="tooltip" 
                                id="finishAplicability"
                                title="Finalizar" onclick="setInAudit();">
                                <i class="fa fa-check-circle-o fa-lg"></i>
                                Finalizar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row progressMatterArea d-none">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Materia</label>
                                <div id="matterTitle"></div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Estatus Materia</label>
                                <div id="statusTitle"></div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Aspectos</label>
                                <div id="advanceTitle"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row progressMatterArea d-none">
                        <div class="col">
                            <div id="bar" class="progress">
                                <div id="progressBarMatter" class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span id="barPercentMatter"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table id="aspectsRegisterTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Materia</th>
                                            <th class="text-center">Aspecto</th>
                                            <th class="text-center">Estatus Aspecto</th>
                                            <th class="text-center">Competencia</th>
                                            <th class="text-center" id="actionEvaluation">Acciones</th>
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
        </div>
    </div>
</div>
