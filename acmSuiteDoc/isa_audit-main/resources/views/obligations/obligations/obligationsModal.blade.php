<div id="obligationsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="obligationsModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="obligationsModalTitle" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd();">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="obligationsForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
                    @php 
                        $filters = array( 
                            'prefix' => '', 
                            'required' => 'true'); 
                    @endphp    
                    @switch(Session::get('profile')['id_profile_type'])
                        @case(1) @case(2) 
                            @include('obligations.filter.owner_filter', $filters)
                            @break
                        @case(3) 
                            @include('obligations.filter.cordinator_filter', $filters)
                            @break
                        @case(4) @case(5)
                            @include('obligations.filter.corporative_filter', $filters)
                            @break
                    @endswitch
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Periodo<span class="star">*</span></label>
                                <select
                                    id="idPeriod"
                                    name="idPeriod" 
                                    class="form-control"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value=""></option>
                                    @foreach($periods as $element)
                                        <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Condición<span class="star">*</span></label>
                                <select
                                    id="idCondition"
                                    name="idCondition" 
                                    class="form-control"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value=""></option>
                                    @foreach($conditions as $element)
                                        <option value="{{ $element['id_condition'] }}">{{ $element['condition'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Tipo<span class="star">*</span></label>
                                <select
                                    id="idObType"
                                    name="idObType" 
                                    class="form-control"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value=""></option>
                                    @foreach($obligationTypes as $element)
                                        <option value="{{ $element['id_obligation_type'] }}">{{ $element['obligation_type'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Título<span class="star">*</span></label>
                                <input 
                                    id="title" 
                                    name="title" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Título"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Obligación<span class="star">*</span></label>
                                <textarea 
                                    id="obligation" 
                                    name="obligation" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Obligación"
                                    rows="3"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSubmitObligation" class="btn btn-primary "></button>
                </div>      
            </form>      
        </div>
    </div>
</div>