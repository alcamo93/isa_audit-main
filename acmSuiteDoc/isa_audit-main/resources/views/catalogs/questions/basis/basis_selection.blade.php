<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaBasisSelection', 
                'idToggle' => 'selection-basis' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaBasisSelection">
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Clasificación legal</label>
                            <select 
                                id="filterlegalclassification" 
                                name="filterlegalclassification" 
                                class="form-control" 
                                onchange="setGuidelines(this.value, '#filterGuideline', 'Selecciona un reglamento')"
                                >
                                <option value="">Todos</option>
                                @foreach($legalC as $element)
                                    <option value="{{ $element['id_legal_c'] }}">{{ $element['legal_classification'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Ley/Reglamento/Norma</label>
                            <select 
                                id="filterGuideline" 
                                name="filterGuideline" 
                                class="form-control"
                                placeholder="Selecciona un reglamento" 
                                onchange="reloadBasis()"
                                >
                                <option value="">Selecciona un reglamento</option>
                                @foreach($guidelines as $element)
                                    <option value="{{ $element['id_guideline'] }}">{{ $element['initials_guideline'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Artículo</label>
                            <input id="filterArt" name="filterArt" type="text" class="form-control" 
                                placeholder="Filtar por Artículo" onkeyup="typewatch('reloadBasis()', 1500)"/>
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
                            onclick="helpMain('.toggle-selection-basis', null,'#actionsBasis', '#buttonCloseBasisSelection', 'fundamento', true)"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-success float-right" 
                        id="buttonCloseBasisSelection" onclick="closeBasisSelection('.basisSelection')">
                        Regresar
                    </button>
                </div>
            </div>
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group text-center text-truncate">
                            <label class="font-weight-bold">Pregunta</label>
                            <div class="cursor-help-title" data-toggle="tooltip" id="currentQuestion-p3"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive basis"> 
                    <table id="basisSelectionTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Ley/Reglamento/Norma</th>
                                <th class="text-center">Orden</th>
                                <th class="text-center">Artículo</th>
                                <th class="text-center" id="actionsBasis">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>