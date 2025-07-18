<div id="addModal" class="modal fade add-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo usuario</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setUserForm" method="POST" action="{{ asset('/users/set') }}">
                <div class="modal-body">
                    @php 
                        $modalData = array('word' => 's-' );
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
                                <input id="s-name" name="s-name" type="text" class="form-control" placeholder="Nombre" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Primer apellido<span class="star">*</span></label>
                                <input id="s-secondName" name="s-secondName" type="text" class="form-control" placeholder="Apellido paterno"
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Segundo apellido</label>
                                <input id="s-lastName" name="s-lastName" type="text" class="form-control" placeholder="Apellido materno" 
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Correo<span class="star">*</span></label>
                                <input id="s-email" name="s-email"type="text" class="form-control" placeholder="ejemplo@mail.com" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-email="true" data-msg-email="El formato de correo es incorrecto"
                                    data-rule-maxlength="60" data-msg-maxlength="Máximo 60 caracteres">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Perfil<span class="star">*</span></label>
                                <select id="s-idProfile" name="s-idProfile" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="s-idStatus" name="s-idStatus" class="form-control"
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
                    <button type="submit" id="btnRegister" class="btn btn-primary float-right">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>