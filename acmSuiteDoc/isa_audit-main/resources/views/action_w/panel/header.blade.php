<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-6">
                    <span id="sectionTask" class="font-weight-bold"></span>
                </div>
                <div class="col">
                    <button
                        type="button"
                        id="comebackContracts"
                        class="btn btn-success float-right"
                        data-toggle="tooltip" 
                        title="Regresar" onclick="closePanel();"
                        >
                        Regresar
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Cliente</label>
                        <div class="customerTitle"></div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Planta</label>
                        <div class="corporateTitle"></div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Auditoría</label>
                        <div class="auditTitle"></div>
                    </div>
                </div>
            </div>
            <div class="row d-flex align-content-start flex-wrap">
                <div class="col">
                    <div class="form-group">
                        <label class="font-weight-bold">Alcance</label>
                        <div class="scopeTitle"></div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="font-weight-bold">Requerimiento</label>
                        <div class="titleRequirement"></div>
                    </div>
                </div>
                <div class="forSubrequiremenst col d-none">
                    <div class="form-group">
                        <label class="font-weight-bold">Subrequerimiento</label>
                        <div class="titleSubrequirement"></div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="font-weight-bold">Estatus</label>
                        <div class="titleStatus"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>