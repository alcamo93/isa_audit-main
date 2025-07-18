<div class="probabilities-section d-none">
    <div class="row">
        <div class="col">
            <?php 
                $text = array( 
                    'title' => 'Área de filtrado', 
                    'tooltip' => ' area de filtrado', 
                    'idElement'=> '#filterAreaProbabilities', 
                    'idToggle' => 'probabilities' ); 
            ?>
            @include('components.toggle.toggle', $text)
            <div class="card gone" id="filterAreaProbabilities" style="display: none;" >
                <div class="card-body" >
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Nombre</label> 
                                <input id="filterProbability" name="filterProbability" type="text" class="form-control" 
                                    placeholder="Filtar por nombre" onkeyup="typewatch('reloadProbabilities()', 1500)"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus</label>
                                <select id="filterIdStatusProbability" name="filterIdStatusProbability" class="form-control"
                                        title="Selecciona una opción" onchange="reloadProbabilities()">
                                    <option value="">Todos</option>
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
            <div class="card" >
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <button 
                                type="button"
                                class="close my-close"
                                data-toggle="tooltip"
                                title="Ayuda"
                                onclick="helpMain('.toggle-probabilities', '#buttonAddProbability', '#actionsProbabilities')"
                                >
                                <i class="fa fa-question-circle-o"></i>
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-success float-right element-hide" 
                                id="buttonAddProbability" onclick="openProbabilityModal()">
                                Agregar<i class="fa fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-success float-right mr-2" 
                                id="closeProbability" onclick="closeProbabilities()">
                                Regresar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive"> 
                        <table id="probabilityTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Probabilidad</th>
                                    <th class="text-center">Probabilidad</th>
                                    <th class="text-center">Estatus</th>
                                    <th class="text-center" id="actionsProbabilities">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>