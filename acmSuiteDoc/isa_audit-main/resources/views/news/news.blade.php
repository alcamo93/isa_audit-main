@extends('theme.master')
@section('view')
    <div id="view">
        @php
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaProfiles', 
                'idToggle' => 'profiles' ); 
        @endphp
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaProfiles" style="display: none;">
            <div class="card-body">
                <div class="row">
                    @switch(Session::get('profile')['id_profile_type'])
                        @case(1) @case(2) 
                            @include('news.filter.owner_filter') 
                        @break
                        @case(3) @case(4) @case(5) 
                            @include('news.filter.customer_filter') 
                        @break
                    @endswitch
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card" id="areaNews" >
                <div class="card-header card-header-title-info">
                    <div class="row">
                        <div class="col">
                            <div class="col">
                                <button type="button" class="close my-close" data-toggle="tooltip"
                                        title="Ayuda" onclick="helpNews();">
                                    <i class="fa fa-question-circle-o"></i>
                                </button>
                            </div>
                            </div>
                            <div class="col">
                             <button
                                id="buttonAddNews"
                                type="button"
                                class="btn btn-success float-right text-white"
                                data-toggle="modal"
                                data-target="#addModal"
                                data-backdrop="static"
                                data-keyboard="false"
                                data-step="1"
                                data-intro="Agregar nueva noticia"
                                data-position='bottom'
                                data-scrollTo='tooltip'
                                onclick="openAddNew()">
                                Agregar<i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive" data-step="2" data-intro="Listado de noticias" data-position='top' data-scrollTo='tooltip'>
                        <table id="newsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Titular</th>
                                    <th class="text-center">Fecha de inicio</th>
                                    <th class="text-center">Fecha de fin</th>
                                    <th class="text-center" id="actionsNews" data-step="3" data-intro="Acciones para registros" data-position='top' data-scrollTo='tooltip'>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        @include('components.loading')
    </div>
    @include('news.modals.addModal')
    @include('news.modals.editModal')
    @include('news.modals.cropModal')
@endsection
@section('javascript')
@parent
@include('components.toggle.toggle_js')
@include('components.components_js')
@include('news.config_permissions')
@include('components.get_corporates_js')
@include('components.validate_js')
@include('news.news_js')
@endsection