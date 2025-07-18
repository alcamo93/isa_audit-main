@extends('theme.master')
@section('view')
    <div id="view">
        <div class="row">
            <div class="col">
                <?php 
                    $text = array( 
                        'title' => 'Área de filtrado', 
                        'tooltip' => ' area de filtrado', 
                        'idElement'=> '#filterAreaIndustries', 
                        'idToggle' => 'industries' ); 
                ?>
                @include('components.toggle.toggle', $text)
                <div class="card gone" id="filterAreaIndustries" style="display: none;" >
                    <div class="card-body" >
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label>Nombre</label> 
                                    <input id="filterName" name="filterName" type="text" class="form-control" 
                                        placeholder="Filtar por nombre" onkeyup="typewatch('reloadIndustries()', 1500)"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card" >
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <button 
                                    type="button"
                                    class="close my-close"
                                    data-toggle="tooltip"
                                    title="Ayuda"
                                    onclick="helpMain('.toggle-industries', '#buttonAddIndustry', '#actionsIndustries')"
                                    >
                                    <i class="fa fa-question-circle-o"></i>
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-success float-right element-hide" 
                                    id="buttonAddIndustry" onclick="openIndustryModel()">
                                    Agregar<i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"> 
                            <table id="industryTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th class="text-center" id="actionsIndustries">Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('catalogs.industries.addModal')
    @include('catalogs.industries.editModal')
    @include('components.loading')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('catalogs.industries.config_permissions')
@include('components.validate_js')
@include('catalogs.industries.industries_js')
@include('components.help.helpModule')
@endsection