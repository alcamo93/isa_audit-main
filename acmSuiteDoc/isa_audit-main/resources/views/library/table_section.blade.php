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
                            onclick="helpMain('.toggle-document', '#buttonAddDocument', '#actionsDocument')">
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right" id="buttonAddDocument">
                            Agregar <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="libraryTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Nombre del documento</th>
                                <th>Fecha</th>
                                <th>Modulo</th>
                                <th>Origen</th>
                                <th class="text-center" id="actionsDocument">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('components.loading')
</div>
