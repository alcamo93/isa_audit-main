<!-- Show aspects by matter -->
<div class="matters-list-card d-none">
    <div class="row">
        <div class="col">
            @php 
                $text = array( 
                    'title' => 'Área de filtrado', 
                    'tooltip' => ' area de filtrado', 
                    'idElement'=> '#filterEvaluation', 
                    'idToggle' => 'evaluation' ); 
            @endphp
            @include('components.toggle.toggle', $text)
            <div class="card" id="filterEvaluation" >
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
                                <select id="idAuditMatter" name="idAuditMatter" class="form-control"
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
                    <input type="hidden" name="idContract" id="idContract"> 
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
                                title="Regresar"
                                id="closeEvaluation"
                                onclick="closeAuditMatters();"
                                >
                                Regresar
                            </button>
                            @endif
                            <button
                                type="button"
                                class="btn btn-success float-right ml-2"
                                data-toggle="tooltip"
                                id="reportAuditProgress"
                                title="Reporte de Progreso de Auditoría">
                                <i class="fa fa-file-excel-o fa-lg"></i>
                                Progreso
                            </button>
                            <button 
                                type="button"
                                class="btn btn-success float-right ml-2"
                                data-toggle="tooltip" 
                                id="reportAuditDocument"
                                title="Reporte de documentos requeridos" 
                                onclick="reportAuditDocument();">
                                <i class="fa fa-file-excel-o fa-lg"></i>
                                Documentos
                            </button>
                            <button 
                                type="button"
                                class="d-none btn btn-success float-right ml-2"
                                data-toggle="tooltip" 
                                id="reportAudit"
                                title="Reporte de auditoria" onclick="reportAudit();">
                                <i class="fa fa-file-excel-o fa-lg"></i>
                                Reporte
                            </button>
                            <button 
                                type="button"
                                class="d-none btn btn-success float-right"
                                data-toggle="tooltip" 
                                id="finishAudit"
                                title="Finalizar" onclick="setInActionPlan();">
                                <i class="fa fa-check-circle-o fa-lg"></i>
                                Finalizar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row progressMatterArea d-none">
                        <div class="col-sm-6 col-md-6 col-lg-3">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Materia</label>
                                <div id="matterTitle"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Estatus Materia</label>
                                <div id="statusTitle"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Cumplimiento</label>
                                <div id="complianceTitle"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Aspectos</label>
                                <div id="advanceTitle"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row progressGlobal">
                        <div class="col">
                            <div class="form-group text-center">
                                <label class="font-weight-bold">Cumplimiento Global</label>
                                <div id="globalTitle"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                                            <th>Materia</th>
                                            <th>Aspecto</th>
                                            <th class="text-center">Estatus Aspecto</th>
                                            <th class="text-center">Tipo de aplicación</th>
                                            <th class="text-center">Cumplimiento</th>
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