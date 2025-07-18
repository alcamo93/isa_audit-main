<div id="addModal" class="modal fade questions-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleModal" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.questions-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="questionForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row">
{{--                        <div class="col-sm-12 col-md-12 col-lg-6">--}}
{{--                            <div class="form-group">--}}
{{--                                <label>Materia<span class="star">*</span></label>--}}
{{--                                <select --}}
{{--                                    id="IdMatter"--}}
{{--                                    name="IdMatter"--}}
{{--                                    class="form-control"--}}
{{--                                    onchange="setAspects(this.value, '#IdAspect', '', false)"--}}
{{--                                    data-rule-required="true" --}}
{{--                                    data-msg-required="Este campo es obligatorio"--}}
{{--                                    >--}}
{{--                                    <option value=""></option>--}}
{{--                                    @foreach($matters as $element)--}}
{{--                                        <option value="{{ $element['id_matter'] }}">{{ $element['matter'] }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-12 col-md-12 col-lg-6">--}}
{{--                            <div class="form-group">--}}
{{--                                <label>Aspecto<span class="star">*</span></label>--}}
{{--                                <select --}}
{{--                                    id="IdAspect"--}}
{{--                                    name="IdAspect"--}}
{{--                                    class="form-control"--}}
{{--                                    data-rule-required="true" --}}
{{--                                    data-msg-required="Este campo es obligatorio"--}}
{{--                                    >--}}
{{--                                    <option value=""></option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Orden<span class="star">*</span></label>
                                <input 
                                    id="order" 
                                    name="order" 
                                    type="number" 
                                    class="form-control" 
                                    placeholder="Orden" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    data-msg-min="El valor del campo debe ser mayor a 0"
                                    min="1"
                                    />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label>Permitir respuesta multiple<span class="star">*</span></label>
                                <select
                                    id="allow_multiple"
                                    name="allow_multiple"
                                    class="form-control"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                >
                                    <option value=""></option>
                                    <option value="1">Si, permitir</option>
                                    <option value="0">No, permitir</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label>Tipo de pregunta<span class="star">*</span></label>
                                <select
                                    id="IdQuestionType"
                                    name="IdQuestionType"
                                    class="form-control"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    onchange="setFields(this.value, '#IdState', '#IdCity')"
                                >
                                    <option value=""></option>
                                    @foreach($questionTypes as $element)
                                        <option value="{{ $element['id_question_type'] }}">{{ $element['question_type'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col stateField d-none"> 
                            <div class="form-group">
                                <label>Estado<span class="star">*</span></label>
                                <select 
                                    id="IdState"
                                    name="IdState" 
                                    class="form-control inputs-forms"
                                    onchange="setCities(this.value, '#IdCity', null)"
                                >
                                    <option value="">Ninguno</option>
                                    @foreach($states as $element)
                                        <option value="{{ $element['id_state'] }}">{{ $element['state'] }}</option>
                                    @endforeach
                                </select> 
                            </div>
                        </div>
                        <div class="col cityField d-none"> 
                            <div class="form-group">
                                <label>Ciudad<span class="star">*</span></label>
                                <select 
                                    id="IdCity"
                                    name="IdCity" 
                                    class="form-control inputs-forms"
                                >
                                    <option value="">Ninguno</option>
                                </select> 
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Pregunta<span class="star">*</span></label>
                                <textarea 
                                    id="question" 
                                    name="question" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Pregunta" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Ayuda</label>
                                <textarea 
                                    id="helpQuestion" 
                                    name="helpQuestion" 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Ayuda" 
                                    rows="3"
                                    ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnAddQuestion" class="btn btn-primary "></button>
                </div>      
            </form>      
        </div>
    </div>
</div>