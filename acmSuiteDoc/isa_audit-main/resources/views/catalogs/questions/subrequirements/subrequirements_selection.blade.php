<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#helpQuestionSubrequirementsSelection', 
                'idToggle' => 'selection-subrequirements' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="helpQuestionSubrequirementsSelection" style="display: none;" >
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label>N° de subrequerimiento</label>
                            <input id="filterSubrequirementNumber" name="filterSubrequirementNumber" type="text" class="form-control" 
                                placeholder="Filtar por número" onkeyup="typewatch('reloadSubrequirements()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label>Subrequerimiento</label>
                            <input id="filterSubrequirement" name="filterSubrequirement" type="text" class="form-control" 
                                placeholder="Filtar por requerimiento" onkeyup="typewatch('reloadSubrequirements()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Evidencia</label>
                            <select id="filterIdEvidence-sr" name="filterIdEvidence-sr" class="form-control"
                                onchange="reloadSubrequirements()">
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
                            <select id="filterIdObtainingPeriod-sr" name="filterIdObtainingPeriod-sr" class="form-control"
                                onchange="reloadSubrequirements()">
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
                            <select id="filterIdUpdatePeriod-sr" name="filterIdUpdatePeriod-sr" class="form-control"
                                onchange="reloadSubrequirements()">
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
                            onclick="helpMain('.toggle-selection-subrequirements', null,'#actionsSubrequirements', '#closeSubrequirementsSelection', 'subrequerimiento', true)"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button 
                            type="button" 
                            class="btn btn-success float-right" 
                            id="closeSubrequirementsSelection"
                            onclick="closeSubRequirements()"
                            >
                            Regresar</i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group text-center text-truncate">
                            <label class="font-weight-bold">Pregunta</label>
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentQuestionReq-p2"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group text-center text-truncate">
                            <label class="font-weight-bold">Respuesta</label>
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentAnswerReq-p2"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4"> 
                        <div class="form-group text-center text-truncate">
                            <label class="font-weight-bold">Requerimiento</label>
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentReq-p2"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="subrequirementsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>N° de subrequerimiento</th>
                                <th>Subequerimiento</th>
                                <th class="text-center" id="actionsSubrequirements">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>