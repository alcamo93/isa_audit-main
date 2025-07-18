<div id="addModalAnswer" class="modal fade add-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleAddAnswer" class="modal-title">Agregar Respuesta</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setAnswerForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Respuesta<span class="star">*</span></label>
                                <input id="answer" name="answer" type="text" class="form-control"  
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="150" data-msg-maxlength="Máximo 150 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Valor de respuesta<span class="star">*</span></label>
                                <select 
                                    id="idAnswerValue"
                                    name="idAnswerValue"
                                    class="form-control"
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    >
                                    <option value=""></option>
                                    @foreach($answersValues as $element)
                                        <option value="{{ $element['id_answer_value'] }}">{{ $element['answer_value'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Orden<span class="star">*</span></label>
                                <input 
                                    id="orderAnswer" 
                                    name="orderAnswer" 
                                    type="number" 
                                    class="form-control" 
                                    placeholder="Orden" 
                                    data-rule-required="true" 
                                    data-msg-required="Este campo es obligatorio"
                                    />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary float-right">Registrar</button>
                </div>      
            </form>      
        </div>
    </div>
</div>