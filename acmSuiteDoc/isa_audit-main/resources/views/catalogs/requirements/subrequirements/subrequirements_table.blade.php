<div class="row">
    <div class="col">
        @php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaSubrequirements', 
                'idToggle' => 'subrequirements' ); 
        @endphp 
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaSubrequirements" style="display: none;">
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>N° de subrequerimiento</label>
                            <input id="filterSubrequirementNumber" name="filterSubrequirementNumber" type="text" class="form-control" 
                                placeholder="Filtar por número" onkeyup="typewatch('reloadSubrequirements()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Subrequerimiento</label>
                            <input id="filterSubrequirement" name="filterSubrequirement" type="text" class="form-control" 
                                placeholder="Filtar por requerimiento" onkeyup="typewatch('reloadSubrequirements()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Condición</label>
                            <select id="filterIdCondition-sr" name="filterIdCondition-sr" class="form-control"
                                onchange="reloadSubrequirements()">
                                <option value="">Todos</option>
                                @foreach($conditions as $element)
                                    <option value="{{ $element['id_condition'] }}">{{ $element['condition'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>Periodo de cierre</label>
                            <select id="filterIdObtainingPeriod-sr" name="filterIdObtainingPeriod-sr" class="form-control"
                                onchange="reloadSubrequirements()">
                                <option value="">Todos</option>
                                @foreach($periods as $element)
                                    <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
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
                    <div class="col-sm-12 col-md-12 col-lg-3">
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
                    <div class="col-sm-12 col-md-12 col-lg-3">
                        <div class="form-group">
                            <label>Tipo de subrequerimiento</label>
                            <select id="filterIdRequirementType-sr" name="filterIdRequirementType-sr" class="form-control"
                                onchange="reloadSubrequirements()">
                                <option value="">Todos</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('catalogs.requirements.current_fom')
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
                            onclick="helpMain('.toggle-subrequirements', '#buttonAddSubrequirement', '#actionsSubrequirements', '#buttonCloseAddSubrequirement')"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right element-hide" 
                            id="buttonAddSubrequirement" onclick="openSubrequirimentModel()">
                            Agregar<i class="fa fa-plus"></i>
                        </button>
                        <button 
                            type="button" 
                            class="btn btn-success float-right mr-2" 
                            id="buttonCloseAddSubrequirement"
                            onclick="closeSubRequirements()"
                            >
                            Regresar</i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <div class="form-group text-center">
                            <label class="font-weight-bold">Requerimiento</label>
                            <div id="currentReq"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="subrequirementsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>N° de subrequerimiento</th>
                                <th>Subrequerimiento</th>
                                <th class="text-center" id="actionsSubrequirements">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>