<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaBasisSelectionSr', 
                'idToggle' => 'selected-subrequirement-basis' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaBasisSelectionSr" style="display: none;">
            <div class="card-body" >
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Ley/Reglamento/Norma</label>
                            <select 
                                id="filterGuidelineSelectedRs" 
                                name="filterGuidelineSelectedRs" 
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
                            <label>Clasificación legal</label>
                            <select 
                                id="filterlegalclassificationSelectedRs" 
                                name="filterlegalclassificationSelectedRs" 
                                class="form-control"
                                onchange="setGuidelines(this.value, '#filterGuidelineSelectedRs', 'Todos')"
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
                            <label>Artículo</label>
                            <input id="filterArtSelectedRs" name="filterArtSelectedRs" type="text" class="form-control" 
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
                            onclick="helpMain('.toggle-selected-subrequirement-basis', null,'#actionsBasisSelectedSr', '#buttonCloseBasisSelectedSr', 'fundamento', false, true)"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-success float-right" 
                        id="buttonCloseBasisSelectedSr" onclick="closeBasisSelection('.showBasisSr')">
                        Regresar
                    </button>
                </div>
            </div>
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <div class="form-group text-center">
                            <label id="noReqTitleLabel-p3" class="font-weight-bold"></label>
                            <div id="noReqTitle-p3"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive basis"> 
                    <table id="rsBasisSelectedTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Ley/Reglamento/Norma</th>
                                <th class="text-center">Orden</th>
                                <th class="text-center">Artículo</th>
                                <th class="text-center" id="actionsBasisSelectedSr">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>