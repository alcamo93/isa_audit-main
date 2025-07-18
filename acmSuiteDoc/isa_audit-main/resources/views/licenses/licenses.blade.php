@extends('theme.master')
@section('view')
    <div id="view">
        <div class="row">
            <div class="col">
                <?php 
                    $text = array( 
                        'title' => 'Área de filtrado', 
                        'tooltip' => ' area de filtrado', 
                        'idElement'=> '#filterAreaLicenses', 
                        'idToggle' => 'licences' ); 
                ?>
                @include('components.toggle.toggle', $text)
                <div class="card gone" id="filterAreaLicenses" style="display: none;">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input id="filterLicense" name="filterLicense" type="text" class="form-control" 
                                        placeholder="Filtar por nombre comercial" onkeyup="typewatch('reloadLicenses()', 1500)"/>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-12 col-lg-6">
                                <div class="form-group">
                                    <label>Estatus</label>
                                    <select id="filterIdStatus" name="filterIdStatus" class="form-control"
                                            onchange="reloadLicenses()">
                                        <option value="0">Todos</option>
                                        @foreach($status as $element)
                                            <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
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
                                <button type="button" class="close my-close" data-toggle="tooltip" 
                                    title="Ayuda" onclick="helpMain('.toggle-licences', '#buttonAddLicense', '#actionsLicenses')">
                                    <i class="fa fa-question-circle-o"></i>
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-success float-right element-hide" 
                                        onclick="openAddLicense()" id="buttonAddLicense">
                                        Agregar <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"> 
                            <table id="licensesTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Licencia</th>
                                        <th class="text-center">Nivel Global</th>
                                        <th class="text-center">Nivel Corporativo</th>
                                        <th class="text-center">Nivel Operativo</th>
                                        <th class="text-center">Vigencia</th>
                                        <th class="text-center">Estatus</th>
                                        <th class="text-center" id="actionsLicenses">Acciones</th>
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
    @include('licenses.addModal')
    @include('licenses.editModal')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('licenses.config_permissions')
@include('components.validate_js')
@include('licenses.licenses_js')
@include('components.help.helpModule')
@endsection