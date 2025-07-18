<div class="row">
    <div class="col">
         @php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaAnswers', 
                'idToggle' => 'selection-basis' ); 
        @endphp
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaAnswers" style="display: none;">
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                            <label>Respuesta</label>
                            <input id="filterAnswer" name="filterAnswer" type="text" class="form-control" 
                                placeholder="Filtar por Artículo" onkeyup="typewatch('reloadAnswers()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label>Valor de respuesta</label>
                                <select 
                                    id="filterIdAnswerValue"
                                    name="filterIdAnswerValue"
                                    class="form-control"
                                    onchange="reloadAnswers()"
                                    >
                                    <option value="">Todos</option>
                                    @foreach($answersValues as $element)
                                        <option value="{{ $element['id_answer_value'] }}">{{ $element['answer_value'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <button 
                            type="button"
                            class="close my-close"
                            data-toggle="tooltip"
                            title="Ayuda"
                            onclick="helpMain('.toggle-selection-answers', null,'#actionsAnswers', '#buttonCloseAnswers')"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right ml-1 element-hide" 
                            id="buttonAddAnswer" onclick="openModalAnswer(false)">
                            Agregar<i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-success float-right" 
                            id="buttonCloseAnswers" onclick="closeAnswers()">
                            Regresar
                        </button>
                </div>
                </div>
            </div>
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <div class="form-group text-center text-truncate">
                            <label class="font-weight-bold">Pregunta</label>
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentQuestion-answer"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive answers"> 
                    <table id="answerTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Orden</th>
                                <th class="text-center">Respuesta</th>
                                <th class="text-center" id="actionsAnswers">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>