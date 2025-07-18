@extends('theme.master')
@section('view')
    <div class="section-main">
        <div class="row">
            <div class="col">
                @include('components.toggle.toggle', array(
                    'title' => 'Área de filtrado',
                    'tooltip' => ' area de filtrado',
                    'idElement'=> '#filterLibrary',
                    'idToggle' => 'library' ))
                <div class="card" id="filterLibrary">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <div class="text-center">{{ $customerTrademark }}</div>
                                    <input 
                                        id="filterIdCustomer" 
                                        name="filterIdCustomer" 
                                        class="form-control d-none"
                                        value="{{ $idCustomer }}"
                                    />
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Planta</label>
                                    <select id="filterIdCorporate" name="filterIdCorporate" class="form-control"
                                            onchange="reloadProcesses()">
                                        <option value="0">Todos</option>
                                        @foreach($corporates as $element)
                                            <option value="{{ $element['id_corporate'] }}">{{ $element['corp_tradename'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>CATEGORIA</label>
                                    <select id="filterIdCategory" name="filterIdCategory" class="form-control filter-select">
                                        <option value="0">Todos</option>
                                        @foreach($categories as $row)
                                            <option value="{{ $row['id_category'] }}">{{ $row['category'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>NOMBRE DEL DOCUMENTO</label>
                                    <input class="form-control filter-text" type="text"
                                           name="filterName" id="filterName"
                                           placeholder="Filtrar por nombre"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>MODULO</label>
                                    <select id="filterIdSource" name="filterIdSource" class="form-control filter-select">
                                        <option value="0">Todos</option>
                                        @foreach($sources as $row)
                                            <option value="{{ $row['id_source'] }}">{{ $row['source'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>ORIGEN</label>
                                    <input class="form-control filter-text" type="text"
                                           name="filterOrigin" id="filterOrigin"
                                           placeholder="Filtrar por origen"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('library.table_section')
    </div>
    @include('library.modals.addDocumentModal')
@endsection
@section('javascript')
    @parent
    @include('components.components_js')
    @include('components.toggle.toggle_js')
    @include('components.validate_js')
    @include('components.get_corporates_js')
    @include('library.library_js')
    @include('components.help.helpModule')
@endsection
