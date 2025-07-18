<div id="editModal" class="modal fade edit-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="editModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleEdit" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('edit-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="updateUserForm" method="POST" action="{{ asset('/users/update') }}">
                <input type="hidden" id="idUser" name="idUser">
                <input type="hidden" id="idPerson" name="idPerson">
                <div class="modal-body">
                    @php 
                        $modalData = array('word' => 'u-' );
                    @endphp
                    @switch(Session::get('profile')['id_profile_type'])
                        @case(1) @case(2) 
                            @include('users.modal.owner_modal', $modalData)
                            @break
                        @case(3) 
                            @include('users.modal.customer_modal', $modalData)
                            @break
                        @case(4) @case(5)
                            @include('users.modal.corporative_modal', $modalData)
                            @break
                    @endswitch
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Nombre<span class="star">*</span></label>
                                <input id="u-name" name="u-name" type="text" class="form-control" placeholder="Nombre" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Primer apellido<span class="star">*</span></label>
                                <input id="u-secondName" name="u-secondName" type="text" class="form-control" placeholder="Apellido paterno"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Segundo apellido</label>
                                <input id="u-lastName" name="u-lastName" type="text" class="form-control" placeholder="Apellido materno" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Correo<span class="star">*</span></label>
                                <input id="u-email" name="u-email"type="text" class="form-control" placeholder="ejemplo@mail.com" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-email="true" data-msg-email="El formato de correo es incorrecto"
                                    data-rule-maxlength="60" data-msg-maxlength="Máximo 60 caracteres">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Perfil<span class="star">*</span></label>
                                <select id="u-idProfile" name="u-idProfile" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="u-idStatus" name="u-idStatus" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary float-right">Actualizar</button>
                </div>
            </form>      
        </div>
    </div>
</div>