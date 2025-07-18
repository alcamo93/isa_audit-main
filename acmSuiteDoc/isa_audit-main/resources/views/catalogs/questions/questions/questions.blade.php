<div class="row">
    <div class="col">
        @php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaQuestions', 
                'idToggle' => 'questions' ); 
        @endphp
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaQuestions" style="display: none;">
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Formulario</label>
                            <div>{{ $parameters->name }}</div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Materia</label>
                            <div>{{ $parameters->matter->matter }}</div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Aspecto</label>
                            <div>{{ $parameters->aspect->aspect }}</div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-8">
                        <div class="form-group">
                            <label>Pregunta</label>
                            <input id="filterQuestion" name="filterQuestion" type="text" class="form-control" 
                                placeholder="Filtar por nombre" onkeyup="typewatch('reloadQuestions()', 1500)"/>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Tipo de pregunta</label>
                            <select id="filterIdQuestionType" name="filterIdQuestionType" class="form-control"
                                onchange="reloadQuestions()">
                                <option value="">Todos</option>
                                @foreach($questionTypes as $element)
                                    <option value="{{ $element['id_question_type'] }}">{{ $element['question_type'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6"> 
                        <div class="form-group">
                            <label>Estado</label>
                            <select id="filterIdState" name="filterIdState" class="form-control"
                                onchange="setCities(this.value, '#filterIdCity', reloadQuestions)">
                                <option value="">Todos</option>
                                @foreach($states as $element)
                                    <option value="{{ $element['id_state'] }}">{{ $element['state'] }}</option>
                                @endforeach
                            </select> 
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6"> 
                        <div class="form-group">
                            <label>Ciudad</label>
                            <select id="filterIdCity" name="filterIdCity" class="form-control"
                                onchange="reloadQuestions()">
                            </select> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('catalogs.requirements.current_fom')
<div class="row">
    <div class="col">
        <div class="card" id="filterAreaQuestions" >
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <button 
                          type="button"
                          class="close my-close"
                          data-toggle="tooltip"
                          title="Ayuda"
                          onclick="helpMain('.toggle-questions', '#buttonAddQuestion', '#actionsQuestions')"
                        >
                          <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button
                          type="button"
                          class="btn btn-success float-right ml-1"
                          onclick="redirectToFormList()"
                        >
                          Regresar
                        </button>
                        <button
                          type="button"
                          class="btn btn-success float-right element-hide"
                          id="buttonAddQuestion"
                          onclick="openQuestionModel()"
                        >
                          Agregar <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="questionTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Pregunta</th>
                                <th>Estado</th>
                                <th class="text-center" id="actionsQuestions">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>