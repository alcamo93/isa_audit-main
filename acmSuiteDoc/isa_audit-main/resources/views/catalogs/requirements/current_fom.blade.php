<div class="card mb-2">
    <div class="card-body" >
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-4">
                <div class="form-group">
                    <label class="font-weight-bold">Formulario</label>
                    <div>{{ $parameters->name }}</div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-4">
                <div class="form-group">
                    <label class="font-weight-bold">Materia</label>
                    <div>{{ $parameters->matter->matter }}</div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-4">
                <div class="form-group">
                    <label class="font-weight-bold">Aspecto</label>
                    <div>{{ $parameters->aspect->aspect }}</div>
                </div>
            </div>
        </div>
    </div>
</div>