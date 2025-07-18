<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterArearMatterAspect', 
                'idToggle' => 'matter-aspect' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card" id="filterArearMatterAspect">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Cliente</label>
                            <div id="customerTitle"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Planta</label>
                            <div id="corporateTitle"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Materia</label>
                            <select id="filterIdMatter" name="filterIdMatter" class="form-control"
                                    onchange="setAspects(this.value, '#filterIdAspect', reloadAction)">
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Aspecto</label>
                            <select id="filterIdAspect" name="filterIdAspect" class="form-control"
                                onchange="reloadAction()">
                                <option value="0">Todos</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="idActionRegister" id="idActionRegister">
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
                            title="Ayuda" onclick="helpMain('.toggle-matter-aspect', null, '#actions', '#comebackContracts');">
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button
                            type="button"
                            id="comebackContracts"
                            class="btn btn-success float-right"
                            data-toggle="tooltip" 
                            title="Regresar" onclick="closeAction();"
                            >
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="actionTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-center">No.</th>
                                <th class="text-center">Aspecto</th>
                                <th class="text-center">Requerimiento</th>
                                <th class="text-center">Nivel de Riesgo</th>
                                <th class="text-center">Hallazgo</th>
                                <th class="text-center">Fecha Inicio</th>
                                <th class="text-center">Fecha Cierre</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center">Responsable</th>
                                <th class="text-center" id="actions">Acciones</th>
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