<div id="addCategoryModal" class="modal fade add-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="addCategoryModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleAdd" class="modal-title">Agregar Categoria</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="setCategoryForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Nombre de categoría<span class="star">*</span></label>
                                <input id="s-name" name="s-name" type="text" class="form-control" placeholder="Espeficica la categoría" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="s-idStatus" name="s-idStatus" class="form-control"
                                        title="Selecciona una opción" required>
                                    <option value="" selected disabled>Sin estatus seleccinado</option>
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary ">Registrar</button>
                </div>      
            </form>      
        </div>
    </div>
</div>

<div id="editCategoryModal" class="modal fade edit-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="editCategoryModalIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleEditCategory" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.edit-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="updateCategoryForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Nombre de categoría<span class="star">*</span></label>
                                <input id="u-name" name="u-name" type="text" class="form-control" placeholder="Espeficica la categoría" 
                                    data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                    data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres"/>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Estatus<span class="star">*</span></label>
                                <select id="u-idStatus" name="u-idStatus" class="form-control"
                                        title="Selecciona una opción" required>
                                    @foreach($status as $element)
                                        <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
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


<div id="riskTextual" class="modal fade edit-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="riskTextualIntro">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleRiskTextual" class="modal-title"></h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.edit-Modal')">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form" id="riskTextualForm">
                <div class="modal-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-sm-12 col-md-10 col-lg-10">
                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Limite para Bajo<span class="star">*</span></label>
                                                <input type="number" class="form-control" id="limitOne"
                                                onchange="defineLimitOne(this.value)" min="1"
                                                data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                                data-rule-number="true" data-msg-number="Solo se permiten números"
                                                data-rule-min="0" data-msg-min="No puede ser menor a {0}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">Rango Bajo 
                                                    <span id="limitOneEnd"></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Limite para Medio<span class="star">*</span></label>
                                                <input type="number" class="form-control" id="limitTwo"
                                                onchange="defineLimitTwo(this.value)"
                                                data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                                data-rule-number="true" data-msg-number="Solo se permiten números"
                                                data-rule-min="0" data-msg-min="No puede ser menor a {0}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">Rango Medio 
                                                    <span id="limitTwoStart"></span>
                                                    <span id="limitTwoEnd"></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Limite para Alto<span class="star">*</span></label>
                                                <input type="number" class="form-control" id="limitThree"
                                                onchange="defineLimitThree(this.value)" disabled value="0"
                                                data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                                data-rule-number="true" data-msg-number="Solo se permiten números">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">Rango Alto 
                                                    <span id="limitThreeStart"></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
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


