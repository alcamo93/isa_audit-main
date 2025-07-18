@extends('theme.master')
@section('view')
    <div id="view">
        <!-- Customers registers to aplicability process -->
        <div class="main-register">
            <div class="row">
                <div class="col">
                    <?php 
                        $text = array( 
                            'title' => 'Área de filtrado', 
                            'tooltip' => ' area de filtrado', 
                            'idElement'=> '#filterAreaAplicability', 
                            'idToggle' => 'aplicability' ); 
                    ?>
                    @include('components.toggle.toggle', $text)
                    <div class="card" id="filterAreaAplicability" style="display: none;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select id="filterIdCustomer" name="filterIdCustomer" class="form-control"
                                                onchange="setCorporates(this.value, '#filterIdCorporate', reloadAplicabilityRegister)">
                                            <option value="0">Todos</option>
                                            @foreach($customers as $element)
                                                <option value="{{ $element['id_customer'] }}">{{ $element['cust_trademark'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label>Planta</label>
                                        <select id="filterIdCorporate" name="filterIdCorporate" class="form-control">
                                            <option value="0">Todos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4">
                                    <div class="form-group">
                                        <label>Estatus</label>
                                        <select id="filterIdStatus" name="filterIdStatus" class="form-control"
                                                onchange="reloadAplicabilityRegister()">
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
                                        title="Ayuda" onclick="helpMain('.toggle-aplicability', null, '#actionsAplicability');">
                                        <i class="fa fa-question-circle-o"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive maintable">
                                <table id="aplicabilityRegisterTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Planta</th>
                                            <th class="text-center">Contrato</th>
                                            <th class="text-center">Estatus</th>
                                            <th class="text-center" id="actionsAplicability">Acciones</th>
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
        @include('aplicability.info.info')
        @include('aplicability.quiz.quiz')
        @include('components.loading')
    </div>
    @include('aplicability.quiz.modal')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('components.validate_js')
@include('components.get_corporates_js')
@include('aplicability.aplicability_js')
@include('aplicability.ownerView.main_js')
@include('aplicability.info.info_js')
@include('aplicability.quiz.quiz_js')
@include('components.help.helpModule')
@endsection