@extends('theme.master')
@section('view')
    <div id="view">
        <div class="row">
            <div class="col">
                @php
                    $text = array( 
                        'title' => 'Área de filtrado', 
                        'tooltip' => ' area de filtrado', 
                        'idElement'=> '#filterAreaUsers', 
                        'idToggle' => 'users' ); 
                @endphp
                @include('components.toggle.toggle', $text)
                <div class="card gone" id="filterAreaUsers" style="display:none;" >
                    <div class="card-body">
                        <div class="row">
                        @switch(Session::get('profile')['id_profile_type'])
                            @case(1) @case(2) 
                                @include('users.filter.owner_filter')
                                @break
                            @case(3) 
                                @include('users.filter.customer_filter')
                                @break
                            @case(4) @case(5)
                                @include('users.filter.corporative_filter')
                                @break
                        @endswitch
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
                                    title="Ayuda" onclick="helpMain('.toggle-users', '#buttonAddUsers', '#actionsUsers');">
                                    <i class="fa fa-question-circle-o"></i>
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-success float-right text-white"
                                    onclick="openAddUser()" id="buttonAddUsers">
                                    Agregar <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center"></th>
                                        <th>Nombre</th>
                                        <th>Apellidos</th>
                                        <th>Correo</th>
                                        <th>Teléfono</th>
                                        <th>Perfil</th>
                                        <th class="text-center">Estatus</th>
                                        <th class="text-center" id="actionsUsers">Acciones</th>
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
    @include('users.addModal')
    @include('users.editModal')
    @include('users.passwordModal')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('users.config_permissions')
@include('components.validate_js')
@include('components.get_corporates_js')
@include('users.users_js')
@include('users.helpModule')
@include('components.help.helpModule')
@endsection