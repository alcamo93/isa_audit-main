<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaMatters', 
                'idToggle' => 'matters' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaMatters" style="display: none;" >
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Materia</label>
                            <input id="filterMatter" name="filterMatter" type="text" class="form-control" 
                                placeholder="Filtar por nombre" onkeyup="typewatch('reloadMatters()', 1500)"/>
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
                            onclick="helpMain('.toggle-matters', '#buttonAddMatter', '#actionsMatters')"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right element-hide" 
                            id="buttonAddMatter" onclick="openMattersModel()">
                            Agregar<i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="mattersTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Materias</th>
                                <th class="text-center" id="actionsMatters">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>