@extends('theme.master')
@section('view')
    <div id="view">
        <div class="row">
            <div class="col">
                <?php 
                    $text = array( 
                        'title' => 'Área de filtrado', 
                        'tooltip' => ' area de filtrado', 
                        'idElement'=> '#filterAreaCorporates', 
                        'idToggle' => 'corporates' ); 
                ?>
                @include('components.toggle.toggle', $text)
                <div class="card gone" id="filterAreaCorporates" style="display: none;" >
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Nombre Comercial</label>
                                    <input id="filterTradename" name="filterTradename" type="text" class="form-control" 
                                        placeholder="Filtar por nombre comercial" onkeyup="typewatch('reloadCorporates()', 1500)"/>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Razón social</label>
                                    <input id="filterTrademark" name="filterTrademark" type="text" class="form-control" 
                                        placeholder="Filtar por razón social" onkeyup="typewatch('reloadCorporates()', 1500)"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label>Estatus</label>
                                    <select id="filterIdStatus" name="filterIdStatus" class="form-control"
                                            onchange="reloadCorporates()">
                                        <option value="0">Todos</option>
                                        @foreach($status as $element)
                                            <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label>Giro</label>
                                    <select id="filterIdIndustry" name="filterIdIndustry" class="form-control"
                                            onchange="reloadCorporates()">
                                        <option value="0">Todos</option>
                                        @foreach($industries as $element)
                                            <option value="{{ $element['id_industry'] }}">{{ $element['industry'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4">
                                <div class="form-group">
                                    <label>RFC</label>
                                    <input id="filterRFC" name="filterRFC" type="text" class="form-control" 
                                    placeholder="Filtar por RFC" onkeyup="typewatch('reloadCorporates()', 1500)"/>
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
                                <button type="button" class="close my-close" data-toggle="tooltip" 
                                        title="Ayuda" onclick="helpMain('.toggle-corporates', '#buttonAddCorporate', '#actionsCorporates', '#buttonCloseCorporates')">
                                    <i class="fa fa-question-circle-o"></i>
                                </button>
                            </div>
                            <div class="col">                                       
                                <button 
                                    type="button" 
                                    class="btn btn-success float-right element-hide" 
                                    onclick="openAddCorporate()"
                                    id="buttonAddCorporate"> 
                                    Agregar <i class="fa fa-plus"></i>
                                </button>
                                <button 
                                    type="button" 
                                    class="btn btn-success float-right mr-2" 
                                    id="buttonCloseCorporates"
                                    onclick="closeCorporates()"
                                    >
                                    Regresar</i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"> 
                            <table id="corporatesTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Razón social</th>
                                        <th>Planta</th>
                                        <th>RFC</th>
                                        <th class="text-center">Estatus</th>
                                        <th class="text-center">Giro</th>
                                        <th class="text-center" id="actionsCorporates">Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('components.loading')
    </div>
    @include('customers.corporates.addModalCorporate')
    @include('customers.corporates.editModalCorporate')
    @include('customers.corporates.addressModal')
    @include('customers.corporates.contactModal')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('customers.corporates.config_permissions')
@include('components.validate_js')
@include('components.locations_js')
@include('customers.corporates.corporates_js')
@include('components.help.helpModule')
@include('customers.corporates.helpModule')
@endsection
