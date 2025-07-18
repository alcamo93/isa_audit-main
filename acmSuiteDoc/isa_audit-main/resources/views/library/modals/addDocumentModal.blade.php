<div
    id="addModal"
    class="modal fade add-Modal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div
        id="addModalIntro"
        class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="titleAdd" class="modal-title">Agregar documento</h5>
                <span>
                    <a type="button" class="close my-close" data-toggle="tooltip" title="Ayuda" onclick="helpAdd('.add-Modal');">
                        <i class="fa fa-question-circle-o"></i>
                    </a>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form class="form" id="formDocument" method="POST">
                <div class="modal-body">
                    @php
                        $modalData = array('word' => 's-' );
                    @endphp
                    @switch(Session::get('profile')['id_profile_type'])
                        @case(1)
                        @case(2)
                            @include('library.modals.filters.owner_modal', $modalData)
                            @break
                        @case(3)
                            @include('library.modals.filters.customer_modal', $modalData)
                            @break
                        @case(4)
                        @case(5)
                            @include('library.modals.filters.corporative_modal', $modalData)
                            @break
                    @endswitch
                    <div class="row">
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <div class="form-group">
                                <label>Categoría<span class="star">*</span></label>
                                <select id="idCategory"
                                        name="idCategory"
                                        class="form-control"
                                        data-rule-required="true"
                                        data-msg-required="Este campo es obligatorio">
                                    <option value="">Seleccionar categoria</option>
                                    @foreach($categories as $row)
                                        <option value="{{ $row['id_category'] }}">{{ $row['category'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-7 col-md-7 col-lg-7">
                            <div class="form-group">
                                <label>Nombre del documento<span class="star">*</span></label>
                                <input class="form-control" type="text"
                                       name="nameFile" id="nameFile"
                                       placeholder="Nombre del documento"
                                       data-rule-required="true"
                                       data-msg-required="Este campo es obligatorio"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <label>Cargar documento<span class="star">*</span></label>
                            <div class="custom-file form-group">
                                <input type="file" class="custom-file-input required" id="idDocumentFile" name="idDocumentFile"
                                       data-rule-required="true" data-msg-required="Este campo es obligatorio"
                                       accept=".zip,.pdf,.rar,.dwg,.xlsx,.xls,.doc,.docx,.xlsm,.pptx,.txt,.jpg,.png,.mp4,.html,.msg,.jpeg,.csv">
                                <label id="idDocumentFileLabel" class="custom-file-label text-truncate">Elige un documento</label>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Enlazar</label>
                                <select id="idSoruce" name="idSoruce" class="form-control">
                                    @foreach($sources as $row)
                                        <option value="{{ $row['id_source'] }}">{{ $row['source'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Planes de acción u obligaciones disponibles</label>
                                <select
                                    disabled
                                    id="idAuditory"
                                    name="idAuditory"
                                    class="form-control"
                                    data-rule-required="true"
                                    data-msg-required="Este campo es obligatorio">
                                    <option value="">Seleccionar plane de accion u obligacione disponibles</option>
                                </select>
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
