<!-- Add users assigned -->
<div id="asignedModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="assignedModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignedTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="asignedForm" >
                <div class="modal-body">
                    @php 
                        $selectors = array( 
                            'idUser' => 'idUser', 
                            'days' => 'days',
                            'function' => 'removeUser',
                            'object' => 'userAssigned',
                            'typeTitle' => 'cierre',
                            'idBtn' => 'btnTrash',
                            'classDisabled' => 'acm-obg-control'); 
                    @endphp
                    @include('action_w.components.usersAssigned', $selectors)
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnAssigned" class="btn btn-primary float-right">Asignar</button>
                </div>
            </form>
        </div>
    </div>
</div>