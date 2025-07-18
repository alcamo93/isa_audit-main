<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#helpQuestionRequirementsSelection', 
                'idToggle' => 'selection-requirements' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="helpQuestionRequirementsSelection" style="display: none;">
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label>N° de requerimiento</label>
                            <input id="filterRequirementNumber" name="filterRequirementNumber" type="text" class="form-control" 
                                placeholder="Filtar por número" onkeyup="typewatch('reloadRequirements()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label>Requerimiento</label>
                            <input id="filterRequirement" name="filterRequirement" type="text" class="form-control" 
                                placeholder="Filtar por requerimiento" onkeyup="typewatch('reloadRequirements()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Evidencia</label>
                            <select id="filterIdEvidence-r" name="filterIdEvidence-r" class="form-control"
                                onchange="reloadRequirements()">
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
                            <select id="filterIdObtainingPeriod-r" name="filterIdObtainingPeriod-r" class="form-control"
                                onchange="reloadRequirements()">
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
                            <select id="filterIdUpdatePeriod-r" name="filterIdUpdatePeriod-r" class="form-control"
                                onchange="reloadRequirements()">
                                <option value="">Todos</option>
                                @foreach($periods as $element)
                                    <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row d-none" id="areaFilterCustomer">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Cliente</label>
                            <select id="filterIdCustomer" name="filterIdCustomer" class="form-control"
                                    onchange="setCorporates(this.value, '#filterIdCorporate', reloadRequirements)">
                                <option value="0">Todos</option>
                                @foreach($customers as $element)
                                    <option value="{{ $element['id_customer'] }}">{{ $element['cust_trademark'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Planta</label>
                            <select id="filterIdCorporate" name="filterIdCorporate" class="form-control"
                                onchange="reloadRequirements()">
                                <option value="0">Todos</option>
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
                            onclick="helpMain('.toggle-selection-requirements', null,'#actionsRequirements', '#closeRequirementsSelection', 'requerimiento', true, false, '#enterSubrequirements')"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right" id="closeRequirementsSelection"
                           onclick="closeRequirementSelection()">
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
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentQuestion-p2"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group text-center text-truncate">
                            <label class="font-weight-bold">Respuesta</label>
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentAnswer-p2"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="requirementsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>N° de requerimiento</th>
                                <th>Requerimiento</th>
                                <th class="text-center" id="enterSubrequirements">Subrequerimientos</th>
                                <th class="text-center" id="actionsRequirements">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>