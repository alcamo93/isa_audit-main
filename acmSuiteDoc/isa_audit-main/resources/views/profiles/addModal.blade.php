<div id="addModal" class="modal fade add-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo perfil</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setProfileForm" method="POST" action="{{ asset('/profiles/set') }}">
                <div class="modal-body">
                    <?php
                        $modalData = array('word' => 's-' );
                        switch (Session::get('profile')['id_profile_type']){
                            case 1: case 2:
                    ?>
                                @include('profiles.modal.owner_modal', $modalData)
                    <?php 
                            break;
                            
                            case 3:
                    ?>
                                @include('profiles.modal.customer_modal', $modalData)
                    <?php 
                            break;
                            
                            case 4: case 5:
                    ?> 
                                @include('profiles.modal.corporative_modal', $modalData)
                    <?php
                    
                            break;
                        }
                    ?>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Perfil<span class="star">*</span></label>
                                <input id="s-profile" type="text" class="form-control" placeholder="Escriba un identificador" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="50" data-msg-maxlength="Máximo 50 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="s-idStatus" class="form-control">
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Nivel de Perfil<span class="star">*</span></label>
                                <select id="s-typeProfile" class="form-control">
                                    @foreach($profilesType as $element)
                                        <option value="{{ $element['id_profile_type'] }}">{{ $element['type'] }}</option>
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