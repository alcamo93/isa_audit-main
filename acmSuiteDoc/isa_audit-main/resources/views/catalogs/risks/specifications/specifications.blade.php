<div class="specifications-section d-none">
    <div class="row">
        <div class="col">
            <?php 
                $text = array( 
                    'title' => 'Área de filtrado', 
                    'tooltip' => ' area de filtrado', 
                    'idElement'=> '#filterAreaSpecifications', 
                    'idToggle' => 'specifications' ); 
            ?>
            @include('components.toggle.toggle', $text)
            <div class="card gone" id="filterAreaSpecifications" style="display: none;" >
                <div class="card-body" >
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Nombre</label> 
                                <input id="filterSpecification" name="filterSpecification" type="text" class="form-control" 
                                    placeholder="Filtar por nombre" onkeyup="typewatch('reloadSpecifications()', 1500)"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Estatus</label>
                                <select id="filterIdStatusSpecification" name="filterIdStatusSpecification" class="form-control"
                                        title="Selecciona una opción" onchange="reloadSpecifications()">
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
                                onclick="helpMain('.toggle-specifications', '#buttonAddSpecification', '#actionsSpecifications')"
                                >
                                <i class="fa fa-question-circle-o"></i>
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-success float-right element-hide" 
                                id="buttonAddSpecification" onclick="openSpecificationModal()">
                                Agregar<i class="fa fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-success float-right mr-2" 
                                id="closeSpecification" onclick="closeSpecification()">
                                Regresar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive"> 
                        <table id="specificationTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Especificación</th>
                                    <th class="text-center" id="actionsSpecifications">Estatus</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>