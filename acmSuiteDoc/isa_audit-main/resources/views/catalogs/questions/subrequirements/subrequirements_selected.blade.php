<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#helpQuestionSubrequirementsSelected', 
                'idToggle' => 'selected-subrequirement' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="helpQuestionSubrequirementsSelected" style="display: none;" >
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label>N° de subrequerimiento</label>
                            <input id="filterSubrequirementNumber-srs" name="filterSubrequirementNumber-srs" type="text" class="form-control" 
                                placeholder="Filtar por número" onkeyup="typewatch('reloadSelectedSubrequirements()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label>Subrequerimiento</label>
                            <input id="filterSubrequirement-srs" name="filterSubrequirement-srs" type="text" class="form-control" 
                                placeholder="Filtar por requerimiento" onkeyup="typewatch('reloadSelectedSubrequirements()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Evidencia</label>
                            <select id="filterIdEvidence-srs" name="filterIdEvidence-srs" class="form-control"
                                onchange="reloadSelectedSubrequirements()">
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
                            <select id="filterIdObtainingPeriod-srs" name="filterIdObtainingPeriod-srs" class="form-control"
                                onchange="reloadSelectedSubrequirements()">
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
                            <select id="filterIdUpdatePeriod-srs" name="filterIdUpdatePeriod-srs" class="form-control"
                                onchange="reloadSelectedSubrequirements()">
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
                            onclick="helpMain('.toggle-selected-subrequirement', null,'#actionsSelectedSubrequirements', '#closeSelectedSubRequirements', 'subrequerimiento', false, true)"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button 
                            type="button" 
                            class="btn btn-success float-right" 
                            id="closeSelectedSubRequirements"
                            onclick="closeSelectedSubRequirements()"
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
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentQuestionReq-p1"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group text-center text-truncate">
                            <label class="font-weight-bold">Respuesta</label>
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentAnswerReq-p1"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group text-center text-truncate">
                            <label class="font-weight-bold">Requerimiento</label>
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentReq-p1"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="selectedSubrequirementsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>N° de subrequerimiento</th>
                                <th>Subequerimiento</th>
                                <th class="text-center" id="actionsSelectedSubrequirements">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>