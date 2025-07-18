<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterObligations', 
                'idToggle' => 'obligations' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterObligations" style="display: none;">
            <div class="card-body" data-step="1" data-intro="Filtros para obligaciones" data-position='bottom' data-scrollTo='tooltip'>
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label class="">Obligación</label>
                            <input id="filterObligation" name="filterObligation" type="text" class="form-control" 
                                placeholder="Filtar por obligación" onkeyup="typewatch('reloadObligations()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label class="">Estatus</label>
                            <select id="filterStatus" onchange="reloadObligations()" class="form-control" >
                                <option value="">Todos</option>
                                @foreach($status as $element)
                                    <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label class="">Periodos</label>
                            <select id="filterPeriod" name="filterPeriod" type="text" class="form-control" 
                                placeholder="Filtar por obligación" onchange="reloadObligations()">
                                <option value="">Todos</option>
                                @foreach($periods as $element)
                                    <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label>Condición</label>
                            <select id="filterIdCondition" name="filterIdCondition" class="form-control"
                                onchange="reloadObligations()">
                                <option value="">Todos</option>
                                @foreach($conditions as $element)
                                    <option value="{{ $element['id_condition'] }}">{{ $element['condition'] }}</option>
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
                            <?php if(Session::get('profile')['id_profile_type'] < 4): ?>
                            onclick="helpMain('.toggle-obligations', '#buttonAddObligation', '#actionsObligations', '#buttonCloseObligations')"
                            <?php else: ?>
                            onclick="helpMain('.toggle-obligations', '#buttonAddObligation', '#actionsObligations')"
                            <?php endif; ?>
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right" 
                            id="buttonAddObligation" onclick="openObligationsModal()">
                            Agregar
                            <i class="fa fa-plus"></i>
                        </button>
                        @if(Session::get('profile')['id_profile_type'] < 4)
                        <button 
                            type="button" 
                            class="btn btn-success float-right mr-2" 
                            id="buttonCloseObligations"
                            onclick="closeObligations()"
                            >
                            Regresar</i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="obligationsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th >Obligación</th>
                                <th >Tipo</th>
                                <th >Requerimientos</th>
                                <th >Fecha de expidición</th>
                                <th >Resgistro de Renovación</th>
                                <th >Registro de Renovación Real</th>
                                <th >Estado</th>
                                <th >Resposable</th>
                                <th class="text-center" id="actionsObligations" >Acciones</th>
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