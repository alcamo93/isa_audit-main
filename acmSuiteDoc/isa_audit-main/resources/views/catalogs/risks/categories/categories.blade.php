<div class="row">
    <div class="col">
        @php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaCategories', 
                'idToggle' => 'categories' ); 
        @endphp
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaCategories" style="display: none;" >
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Nombre</label> 
                            <input id="filterName" name="filterName" type="text" class="form-control" 
                                placeholder="Filtar por nombre" onkeyup="typewatch('reloadCategories()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Estatus</label>
                            <select id="filterIdStatus" name="filterIdStatus" class="form-control"
                                    title="Selecciona una opción" onchange="reloadCategories()">
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
                            onclick="helpMain('.toggle-categories', '#buttonAddCategory', '#actionsCategories')"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right element-hide" 
                            id="buttonAddCategory" onclick="openCategoryModal()">
                            Agregar<i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="categoryTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Categoría de Riesgo</th>
                                <th class="text-center" id="actionsCategories">Estatus</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>