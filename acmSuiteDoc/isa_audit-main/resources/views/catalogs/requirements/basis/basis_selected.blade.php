<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaBasisSelected', 
                'idToggle' => 'selected-basis' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaBasisSelected" style="display: none;">
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Clasificación legal</label>
                            <select 
                                id="filterlegalclassificationSelected" 
                                name="filterlegalclassificationSelected" 
                                class="form-control"
                                onchange="setGuidelines(this.value, '#filterGuidelineSelected', 'Todos')"
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
                                id="filterGuidelineSelected" 
                                name="filterGuidelineSelected" 
                                class="form-control"
                                placeholder="Selecciona un reglamento" 
                                onchange="reloadSelectedBasisTable()"
                                >
                                <option value="">Todos</option>
                                @foreach($guidelines as $element)
                                    <option value="{{ $element['id_guideline'] }}">{{ $element['initials_guideline'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Artículo</label>
                            <input id="filterArtSelected" name="filterArtSelected" type="text" class="form-control" 
                                placeholder="Filtar por Artículo" onkeyup="typewatch('reloadSelectedBasisTable()', 1500)"/>
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
                            onclick="helpMain('.toggle-selected-basis', null,'#actionsBasisSelected', '#buttonCloseBasisSelected', 'fundamento', false, true)"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-success float-right" 
                        id="buttonCloseBasisSelected" onclick="closeBasisSelection('.showBasis')">
                        Regresar
                    </button>
                </div>
            </div>
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <div class="form-group text-center">
                            <label id="noReqTitleLabel-p1" class="font-weight-bold"></label>
                            <div id="noReqTitle-p1"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive basis"> 
                    <table id="basisSelectedTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Ley/Reglamento/Norma</th>
                                <th class="text-center">Orden</th>
                                <th class="text-center">Artículo</th>
                                <th class="text-center" id="actionsBasisSelected">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>