<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaGuidelines', 
                'idToggle' => 'guidelines' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaGuidelines" style="display: none;">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Clasificación</label>
                            <select 
                                id="filter_legal_classification" 
                                name="filter_legal_classification" 
                                class="form-control"
                                onchange="reloadGuidelines()"
                                >
                                <option value="">Todos</option>
                                @foreach($legalC as $element)
                                    <option value="{{ $element['id_legal_c'] }}">{{ $element['legal_classification'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Ley/Reglamento/Norma</label>
                            <input id="filterName" name="filterName" type="text" class="form-control" 
                                placeholder="Filtar por nombre" onkeyup="typewatch('reloadGuidelines()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Siglas</label>
                            <input id="filterInitials" name="filterInitials" type="text" class="form-control" 
                                placeholder="Filtar por Siglas" onkeyup="typewatch('reloadGuidelines()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Competencia</label>
                            <select 
                                id="filter_application_type" 
                                name="filter_application_type" 
                                class="form-control"
                                onchange="reloadGuidelines()"
                                >
                                <option value="">Todos</option>
                                @foreach($appTypes as $element)
                                    <option value="{{ $element['id_application_type'] }}">{{ $element['application_type'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Estado</label>
                            <select 
                                class="form-control" 
                                id="filter_state"
                                name="filter_state"
                                required title="Selecciona una opción"
                                onchange="setCities(this.value, '#filter_city', reloadGuidelines)">
                                <option value="">Todos</option>
                                @foreach($states as $element)
                                    <option value="{{ $element['id_state'] }}">{{ $element['state'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Ciudad</label>
                            <select 
                                class="form-control" 
                                id="filter_city"
                                name="filter_city"
                                required title="Selecciona una opción"
                                onchange="reloadGuidelines()"
                                >
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
                                title="Ayuda" onclick="helpMain('.toggle-guidelines', '#buttonAddGuidelines', '#actionsGuidelines')">
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">                                        
                        <button type="button" class="btn btn-success float-right" 
                            onclick="openAddGuidelines()" id="buttonAddGuidelines"> 
                            Agregar <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="guidelinesTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-left">LEY/REGLAMENTO/NORMA</th>
                                <th class="text-left">Siglas</th>
                                <th class="text-center" id="actionsGuidelines">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
