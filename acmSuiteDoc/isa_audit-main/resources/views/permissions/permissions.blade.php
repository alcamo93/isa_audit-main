@extends('theme.master')
@section('view')
    <div id="view">
        <div class="maintable">
            <?php 
                $text = array( 
                    'title' => 'Área de filtrado', 
                    'tooltip' => ' area de filtrado', 
                    'idElement'=> '#filterAreaPermissions', 
                    'idToggle' => 'permissions' ); 
            ?>
            @include('components.toggle.toggle', $text)
            <div class="card" id="filterAreaPermissions">
                <div class="card-body" data-step="1" data-intro="Filtros para permisos" data-position='bottom' data-scrollTo='tooltip'>
                    <div class="row">
                        <?php 
                            switch(Session::get('profile')['id_profile_type']):
                                case 1: case 2:
                        ?>
                                @include('permissions.filter.owner_filter')
                        <?php  
                                break;

                                case 3:
                        ?>
                                @include('permissions.filter.customer_filter')
                        <?php
                                break;

                                case 4: case 5:
                        ?>
                                @include('permissions.filter.corporative_filter')
                        <?php
                                break;
                            endswitch;
                        ?>
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
                                        title="Ayuda" onclick="helpPermissions();">
                                        <i class="fa fa-question-circle-o"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="permissionsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Módulo</th>
                                            <th class="text-center" id="actionVisualize">Visualizar</th>
                                            <th class="text-center" id="actionCreate">Crear</th>
                                            <th class="text-center" id="actionModify">Modificar</th>
                                            <th class="text-center" id="actionDelete">Eliminar</th>
                                            <th class="text-center" id="actionSubmodule">Submódulos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Submodules permissions -->
        <div class="subtable d-none">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="close my-close" data-toggle="tooltip" 
                                        title="Ayuda" onclick="helpSubPermissions();">
                                        <i class="fa fa-question-circle-o"></i>
                                    </button>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-success float-right subtable d-none" data-toggle="tooltip" 
                                        title="Regresar" id="backToPermissions" onclick="closeSubmodulePermissions();">
                                        Regresar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="permissionsSubmodulesTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Módulo</th>
                                            <th class="text-center">Submódulo</th>
                                            <th class="text-center" id="actionSubVisualize">Visualizar</th>
                                            <th class="text-center" id="actionSubCreate">Crear</th>
                                            <th class="text-center" id="actionSubModify">Modificar</th>
                                            <th class="text-center" id="actionSubDelete">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('components.loading')
    </div>
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('components.get_corporates_js')
@include('permissions.permissions_js')
@include('permissions.helpModule')
@endsection