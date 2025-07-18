<div class="card-header">
        <div class="row">
            <div class="col">
                <button type="button" class="btn btn-success float-right" 
                    id="buttonAddComment" onclick="openCommentsModal()">
                    Agregar Comentario<i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="commentsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>