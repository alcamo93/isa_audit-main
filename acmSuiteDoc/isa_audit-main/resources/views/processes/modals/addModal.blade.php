<div id="addModal" class="modal fade add-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleAdd" class="modal-title">Agregar Auditoría</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setProcessesForm">
                <div class="modal-body">
                    @php 
                        $modalData = array('word' => 's-' );
                    @endphp
                    @switch(Session::get('profile')['id_profile_type'])
                        @case(1) @case(2) 
                            @include('processes.modals.filters.owner_modal', $modalData)
                            @break
                        @case(3) 
                            @include('processes.modals.filters.customer_modal', $modalData)
                            @break
                        @case(4) @case(5)
                            @include('processes.modals.filters.corporative_modal', $modalData)
                            @break
                    @endswitch
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Nombre de Auditoría<span class="star">*</span></label>
                                <input id="s-processes" name="s-processes" type="text" class="form-control"  
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Alcance<span class="star">*</span></label>
                                <select id="s-idScope" name="s-idScope" class="form-control"
                                    onchange="showField(this.value, '#s-specification')"
                                    title="Selecciona una opción" required>
                                    @foreach($scopes as $s)
                                        <option value="{{ $s['id_scope'] }}">{{ $s['scope'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 d-none specificationDepartament">
                            <div class="form-group">
                                <label>Especificar departamento<span class="star">*</span></label>
                                <input id="s-specification" name="s-specification" type="text" class="form-control" placeholder="Nombre del departamento" 
                                    data-rule-required="false" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Auditor Lider<span class="star">*</span></label>
                                <select id="s-idLeader" name="s-idLeader" class="form-control"
                                    onchange="getAuditors(this.value, '#s-auditors', false)"
                                    title="Selecciona una opción" required>
                                    <option value=""></option>
                                    @if( sizeof($auditors) == 0 )
                                        <option value="" disabled>Seleccione una Planta</option>
                                    @else
                                        @foreach($auditors as $s)
                                            <option value="{{ $s['id_user'] }}">{{ $s['complete_name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Auditores</label>
                                <select id="s-auditors" name="s-auditors" class="selectpicker form-control" data-width="100px" 
                                    title="Selecciona una opción o más" multiple>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Materia y Aspecto<span class="star">*</span></label>
                                <select id="s-aspects" name="s-aspects" data-size="5" class="selectpicker form-control" data-width="100px"
                                    title="Selecciona una opción o más" required multiple>
                                    @foreach($matters  as $m)
                                    <optgroup label="Materia: {{ $m['matter'] }}">
                                        @foreach($m['aspects']  as $a)
                                        <option value="{{ $a['id_aspect'] }}">{{ $a['aspect'] }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-check align-check">
                                <label class="form-check-label ">
                                <input class="form-check-input" id="s-risk" type="checkbox"/>
                                    <span class="form-check-sign"></span>
                                    Evaluar nivel de riesgo (Recomendado)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-danger float-right">Cancelar</button>
                    <button type="submit" id="btnRegister" class="btn btn-primary float-right">Registrar</button>
                </div>      
            </form>      
        </div>
    </div>
</div>
<style>
    .bootstrap-select.btn-group .dropdown-menu.inner{
        max-height: 260px !important;
        overflow: hidden;
    }
</style>
