<div id="showModal" class="modal fade add-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleShow" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Cliente</label>
                            <div class="filedsShow font-weight-bold" id="customer"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Planta</label>
                            <div class="filedsShow font-weight-bold" id="corporate"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Nombre de Auditoría</label>
                            <div class="filedsShow font-weight-bold" id="process"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Evaluar Nivel de riesgo</label>
                            <div class="filedsShow font-weight-bold" id="risk"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Alcance</label>
                            <div class="filedsShow font-weight-bold" id="scope"></div>
                        </div>
                    </div>
                    <div class="col d-none specificationDepartament">
                        <div class="form-group">
                            <label>Especificar departamento</label>
                            <div class="filedsShow font-weight-bold" id="specification"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <table id="tableShowAuditors" class="table table-striped table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr class="table-info">
                                        <th class="text-center font-weight-bold text-uppercase" colspan="3">
                                            Auditores
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center">Nombre</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <table id="tableShowAspects" class="table table-striped table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr class="table-info">
                                        <th class="text-center font-weight-bold text-uppercase" colspan="3">
                                            Materia y Aspecto
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Materia</th>
                                        <th class="text-center">Aspecto</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>       
        </div>
    </div>
</div>