<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#helpQuestionRequirementsSelected', 
                'idToggle' => 'selected-requirement' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="helpQuestionRequirementsSelected" style="display: none;">
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label>N° de requerimiento</label>
                            <input id="filterRequirementNumber-rs" name="filterRequirementNumber-rs" type="text" class="form-control" 
                                placeholder="Filtar por número" onkeyup="typewatch('reloadSelectedRequirements()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label>Requerimiento</label>
                            <input id="filterRequirement-rs" name="filterRequirement-rs" type="text" class="form-control" 
                                placeholder="Filtar por requerimiento" onkeyup="typewatch('reloadSelectedRequirements()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Evidencia</label>
                            <select id="filterIdEvidence-rs" name="filterIdEvidence-rs" class="form-control"
                                onchange="reloadSelectedRequirements()">
                                <option value="">Todos</option>
                                @foreach($evidences as $element)
                                    <option value="{{ $element['id_evidence'] }}">{{ $element['evidence'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Periodo de obtención</label>
                            <select id="filterIdObtainingPeriod-rs" name="filterIdObtainingPeriod-rs" class="form-control"
                                onchange="reloadSelectedRequirements()">
                                <option value="">Todos</option>
                                @foreach($periods as $element)
                                    <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Periodo de actualización</label>
                            <select id="filterIdUpdatePeriod-rs" name="filterIdUpdatePeriod-rs" class="form-control"
                                onchange="reloadSelectedRequirements()">
                                <option value="">Todos</option>
                                @foreach($periods as $element)
                                    <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
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
                        <button
                            type="button"
                            class="close my-close"
                            data-toggle="tooltip"
                            title="Ayuda"
                            onclick="helpMain('.toggle-selected-requirement', null,'#actionsRequirements-s', '#closeSelectedRequeriments', 'requerimiento', false, true, '#enterSubrequirements-s')">
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right"
                            id="closeSelectedRequeriments" 
                            onclick="closeSelectedRequeriments()">
                            Regresar</i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group text-center text-truncate">
                            <label class="font-weight-bold">Pregunta</label>
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentQuestion-p1"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group text-center text-truncate">
                            <label class="font-weight-bold">Respuesta</label>
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentAnswer-p1"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="selectedRequirementsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>N° de requerimiento</th>
                                <th>Requerimiento</th>
                                <th class="text-center" id="enterSubrequirements-s">Subrequerimientos</th>
                                <th class="text-center" id="actionsRequirements-s">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>