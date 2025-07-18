<div class="consequences-section d-none">
    <div class="row">
        <div class="col">
            <?php 
                $text = array( 
                    'title' => 'Área de filtrado', 
                    'tooltip' => ' area de filtrado', 
                    'idElement'=> '#filterAreaConsequences', 
                    'idToggle' => 'consequences' ); 
            ?>
            @include('components.toggle.toggle', $text)
            <div class="card gone" id="filterAreaConsequences" style="display: none;" >
                <div class="card-body" >
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Consecuencia</label> 
                                <input id="filterConsequence" name="filterConsequence" type="text" class="form-control" 
                                    placeholder="Filtar por consecuencia" onkeyup="typewatch('reloadConsequences()', 1500)"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus</label>
                                <select id="filterIdStatusConsequence" name="filterIdStatusConsequence" class="form-control"
                                        title="Selecciona una opción" onchange="reloadConsequences()">
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
                                onclick="helpMain('.toggle-consequences', '#buttonAddConsequence', '#actionsConsequences')"
                                >
                                <i class="fa fa-question-circle-o"></i>
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-success float-right element-hide" 
                                id="buttonAddConsequence" onclick="openConsequenceModal()">
                                Agregar<i class="fa fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-success float-right mr-2" 
                                id="closeConsequence" onclick="closeConsequence()">
                                Regresar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive"> 
                        <table id="consequenceTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Consecuencia</th>
                                    <th class="text-center">Consecuencia</th>
                                    <th class="text-center">Estatus</th>
                                    <th class="text-center" id="actionsConsequences">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>