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
            <form class="form" id="updateProfileForm" method="POST" action="{{ asset('/profiles/update') }}">
                <input type="hidden" id="idProfile" name="idProfile">
                <div class="modal-body">
                    <?php
                        $modalData = array('word' => 'u-' );
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
                                <input id="u-profile" type="text" class="form-control" placeholder="Escriba un identificador" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="50" data-msg-maxlength="Máximo 50 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="u-idStatus" class="form-control">
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Nivel de Perfil<span class="star">*</span></label>
                                <select id="u-typeProfile" class="form-control">
                                    @foreach($profilesType as $element)
                                        <option value="{{ $element['id_profile_type'] }}">{{ $element['type'] }}</option>
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