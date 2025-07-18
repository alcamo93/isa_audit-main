@section('css')
@parent
<style>
.jstree-anchor {
    width: 95%;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
</style>
@endsection
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
                        title="Regresar" onclick="closeDependency();"
                        >
                        Regresar
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Pregunta</label>
                        <div class="questionTitle"></div>
                    </div>
                </div>
            </div>
            <div class="row d-flex align-content-start flex-wrap">
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Materia</label>
                        <div class="matterTitle"></div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Aspecto</label>
                        <div class="aspectTitle"></div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Tipo de pregunta</label>
                        <div class="typeTitle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <span class="font-weight-bold">Instrucciones: </span>Selecciona las preguntas que se desbloquearan según la respuesta del usuario     
            <hr>
            <div id="dependencyTreeView">

            </div>
        </div>
    </div>
</div>