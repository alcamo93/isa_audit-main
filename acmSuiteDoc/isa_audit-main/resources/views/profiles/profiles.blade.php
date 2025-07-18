@extends('theme.master')
@section('view')
    <div id="view">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaProfiles', 
                'idToggle' => 'profiles' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaProfiles" style="display: none;">
            <div class="card-body">
                <div class="row">
                <?php
                            switch (Session::get('profile')['id_profile_type']){
                                case 1: case 2:
                        ?>
                                    @include('profiles.filter.owner_filter')
                        <?php 
                                break;
                                
                                case 3:
                        ?>
                                    @include('profiles.filter.customer_filter')
                        <?php 
                                break;
                                
                                case 4: case 5:
                        ?> 
                                    @include('profiles.filter.corporative_filter')
                        <?php
                        
                                break;
                            }
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
                                    title="Ayuda" onclick="helpMain('.toggle-profiles', '#buttonAddProfile', '#actionsProfiles');">
                                    <i class="fa fa-question-circle-o"></i>
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-success float-right element-hide"
                                        onclick="openAddProfile()" id="buttonAddProfile">
                                    Agregar <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="profilesTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Perfil</th>
                                        <th class="text-center">Planta</th>
                                        <th class="text-center">Estatus</th>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center" id="actionsProfiles">Acciones</th>
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
        @include('components.loading')
    </div>
    @include('profiles.addModal')
    @include('profiles.editModal')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('profiles.config_permissions')
@include('components.validate_js')
@include('components.get_corporates_js')
@include('profiles.profiles_js')
@include('components.help.helpModule')
@endsection