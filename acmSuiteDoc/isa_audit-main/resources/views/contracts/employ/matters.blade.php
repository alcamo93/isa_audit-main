<!-- <div class="row section-matters d-none">
    <div class="col">
        <div class="card" id="filterArea">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Materias</label>
                            <select id="filterIdMatter" name="filterIdMatter" class="form-control"
                                    onchange="reloadRegisters()">
                                @foreach($matters as $element)
                                    <option value="{{ $element['id_matter'] }}">{{ $element['matter'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<div class="row section-matters d-none">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">

                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right" data-toggle="tooltip" 
                            title="Regresar" id="backToContracts" onclick="closeMatterSection()">
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <input type="hidden" name="idContract" id="idContract">
                    <input type="hidden" name="idCorporate" id="idCorporate">
                    <input type="hidden" name="idAplicabilityRegister" id="idAplicabilityRegister">
                    <table id="registersMattersTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th class="text-center" id="actionContracted">Contratada</th>
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

<div class="row section-aspects d-none">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right" data-toggle="tooltip" 
                            title="Regresar" id="backToMatters" onclick="closeAspectsSection()">
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <input type="hidden" name="idContractMatter" id="idContractMatter">
                    <input type="hidden" name="idMatter" id="idMatter">
                    <table id="registersAspectsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Aspecto</th>
                                <th class="text-center" id="actionVisualize">Contratada</th>
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