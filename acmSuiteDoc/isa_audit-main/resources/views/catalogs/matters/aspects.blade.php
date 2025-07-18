<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaAspects', 
                'idToggle' => 'aspects' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaAspects" style="display: none;" >
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Aspecto</label>
                            <input id="filterAspects" name="filterAspects" type="text" class="form-control" 
                                placeholder="Filtar por nombre" onkeyup="typewatch('reloadAspects()', 1500)"/>
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
                            onclick="helpMain('.toggle-aspects', '#buttonAddAspect', '#actionsAspects', '#closeAspects')"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right element-hide" 
                            id="buttonAddAspect" onclick="openAspectsModel()">
                            Agregar<i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-success float-right mr-2" 
                            id="closeAspects" onclick="closeAspects()">
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="aspectsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Aspectos</th>
                                <th class="text-center" id="actionsAspects">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>