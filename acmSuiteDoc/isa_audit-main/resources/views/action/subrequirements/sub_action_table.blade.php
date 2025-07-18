<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="form-group text-center">
                            <label class="font-weight-bold">No.</label>
                            <div id="noReqTitle"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group text-center">
                            <label class="font-weight-bold">Requerimiento</label>
                            <div id="reqTitle"></div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="idRequirement" id="idRequirement">
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
                        <!-- <button type="button" class="close my-close" data-toggle="tooltip" 
                            title="Ayuda" onclick="helpAction();">
                            <i class="fa fa-question-circle-o"></i>
                        </button> -->
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right" data-toggle="tooltip" 
                            title="Regresar" onclick="closeSubAction();">
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="subActionTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-center">No.</th>
                                <th class="text-center">Aspecto</th>
                                <th class="text-center">Subrequerimiento</th>
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