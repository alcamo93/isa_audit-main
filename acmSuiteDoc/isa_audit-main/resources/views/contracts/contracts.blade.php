@extends('theme.master')
@section('view')
    <div id="view">
        <div class="row section-main">
            <div class="col">
                <?php 
                    $text = array( 
                        'title' => 'Área de filtrado', 
                        'tooltip' => ' area de filtrado', 
                        'idElement'=> '#filterAreaContracts', 
                        'idToggle' => 'contracts' ); 
                ?>
                @include('components.toggle.toggle', $text)
                <div class="card gone" id="filterAreaContracts" style="display: none;">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Contrato</label>
                                    <input id="filterContract" name="filterContract" type="text" class="form-control" 
                                        placeholder="Filtar por nombre de contrato" onkeyup="typewatch('reloadContracts()', 1500)"/>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Estatus</label>
                                    <select id="filterIdStatus" name="filterIdStatus" class="form-control"
                                            onchange="reloadContracts()">
                                        <option value="0">Todos</option>
                                        @foreach($status as $element)
                                            <option value="{{ $element['id_status'] }}">{{ $element['status'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <select id="filterIdCustomer" name="filterIdCustomer" class="form-control"
                                            onchange="setCorporates(this.value, '#filterIdCorporate', reloadContracts)">
                                        <option value="0">Todos</option>
                                        @foreach($customers as $element)
                                            <option value="{{ $element['id_customer'] }}">{{ $element['cust_trademark'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Planta</label>
                                    <select id="filterIdCorporate" name="filterIdCorporate" class="form-control">
                                        <option value="0">Todos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row section-main">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <button 
                                    type="button"
                                    class="close my-close"
                                    data-toggle="tooltip" 
                                    title="Ayuda"
                                    onclick="helpMain('.toggle-contracts', '#buttonAddContract', '#actionsContracts')"
                                    >
                                    <i class="fa fa-question-circle-o"></i>
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-success float-right element-hide"
                                        onclick="openAddContract()" id="buttonAddContract">
                                    Agregar <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"> 
                            <table id="contractsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Contrato</th>
                                        <th>Cliente</th>
                                        <th>Planta</th>
                                        <th>Fecha de inicio</th>
                                        <th>Fecha de término</th>
                                        <th>Estatus</th>
                                        <th id="actionsContracts">Acciones</th>
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
    @include('contracts.addModal')
    @include('contracts.fullViewModal')
    @include('contracts.updateModal')
    @include('contracts.renewModal')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('contracts.config_permissions')
@include('components.validate_js')
@include('components.get_corporates_js')
@include('contracts.contracts_js')
@endsection