<div class="exhibitions-section d-none">
    <div class="row">
        <div class="col">
            <?php 
                $text = array( 
                    'title' => 'Área de filtrado', 
                    'tooltip' => ' area de filtrado', 
                    'idElement'=> '#filterAreaExhibition', 
                    'idToggle' => 'exhibition' ); 
            ?>
            @include('components.toggle.toggle', $text)
            <div class="card gone" id="filterAreaExhibition" style="display: none;" >
                <div class="card-body" >
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Exposición</label> 
                                <input id="filterExhibition" name="filterExhibition" type="text" class="form-control" 
                                    placeholder="Filtar por exposición" onkeyup="typewatch('reloadExhibitions()', 1500)"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus</label>
                                <select id="filterIdStatusExhibition" name="filterIdStatusExhibition" class="form-control"
                                        title="Selecciona una opción" onchange="reloadExhibitions()">
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
                                onclick="helpMain('.toggle-exhibition', '#buttonAddExhibition', '#actionsExhibitions')"
                                >
                                <i class="fa fa-question-circle-o"></i>
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-success float-right element-hide" 
                                id="buttonAddExhibition" onclick="openExhibitionModal()">
                                Agregar<i class="fa fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-success float-right mr-2" 
                                id="closeExhibition" onclick="closeExhibition()">
                                Regresar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive"> 
                        <table id="exhibitionTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Exposición</th>
                                    <th class="text-center">Exposición</th>
                                    <th class="text-center">Estatus</th>
                                    <th class="text-center" id="actionsExhibitions">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>