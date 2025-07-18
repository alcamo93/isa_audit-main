<div id="userListModal" class="modal fade userList-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="adduserListModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="userListModalTitle" class="modal-title">Usuario asignado</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpUserAsing('.userList-Modal', 'obligación')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="userAsigned">
                <div class="modal-body" id="userAsigned"></div>
                <div class="modal-footer">
                    @if(Session::get('modify'))
                    <button id="btnSelectUser" class="btn btn-primary ">Asignar usuario</button>
                    @endif
                </div>
            </div>
            <div class="usersList">
                <div class="modal-body" id="listUsersSelection"></div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
</div>