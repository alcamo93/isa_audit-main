<div class="row">
    <div class="col">
        @php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaHelp', 
                'idToggle' => 'help' ); 
        @endphp
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaHelp">
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Nombre</label> 
                            <input id="filterNameHelp" name="filterNameHelp" type="text" class="form-control" 
                                placeholder="Filtar por nombre" onkeyup="typewatch('reloadHelp()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Valoración de...</label>
                            <select id="filterAttribute" name="filterAttribute" class="form-control"
                                    title="Selecciona una opción" onchange="reloadHelp()">
                                @foreach($attributes as $element)
                                    <option value="{{ $element['id_risk_attribute'] }}">{{ $element['risk_attribute'] }}</option>
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
        <div class="card" >
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <button 
                            type="button"
                            class="close my-close"
                            data-toggle="tooltip"
                            title="Ayuda"
                            onclick="helpMain('.toggle-help', '#buttonAddHelp', '#actionsHelp')"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right element-hide" 
                            id="buttonAddHelp" onclick="openHelpModal()">
                            Agregar<i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-success float-right mr-2" 
                            id="closeHelp" onclick="closeHelp()">
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="helpTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Criterio</th>
                                <th class="text-center">Valor</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center" id="actionsHelp">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>