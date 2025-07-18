<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaBasis', 
                'idToggle' => 'basis' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaBasis" style="display: none;">
            <div class="card-body">                    
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Artículo</label>
                            <input id="filterBasisName" name="filterBasisName" type="text" class="form-control" 
                                placeholder="Filtar por nombre" onkeyup="typewatch('reloadBasis()', 1500)"/>
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
                        <button type="button" class="close my-close" data-toggle="tooltip" 
                                title="Ayuda" onclick="helpMain('.toggle-basis', '#buttonAddBasis', '#actionsBasis', '#buttonReturnBasis')">
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col"> 
                        <button type="button" class="btn btn-success float-right" 
                            onclick="openAddBasis()" id="buttonAddBasis"> 
                            Agregar <i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-success float-right mr-2" 
                            onclick="returnToGuidelines()" id="buttonReturnBasis"> 
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <div class="form-group text-center">
                            <!-- <label class="font-weight-bold">LEY/REGLAMENTO/NORMA</label> -->
                            <div id="currentGuideline"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="basisTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Orden</th>
                                <th class="text-center">Artículo</th>
                                <th class="text-center" id="actionsBasis">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
