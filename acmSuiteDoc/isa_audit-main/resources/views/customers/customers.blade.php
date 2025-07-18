@extends('theme.master')
@section('view')
    <div id="view">
        <div class="row">
            <div class="col">
                <?php 
                    $text = array( 
                        'title' => 'Área de filtrado', 
                        'tooltip' => ' area de filtrado', 
                        'idElement'=> '#filterAreaCustomers', 
                        'idToggle' => 'customers' ); 
                ?>
                @include('components.toggle.toggle', $text)
                <div class="card gone" id="filterAreaCustomers" style="display: none;">
                    <div class="card-body" >
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label>Razón social</label> 
                                    <input id="filterName" name="filterName" type="text" class="form-control" 
                                        placeholder="Filtar por nombre comercial o razón social" onkeyup="typewatch('reloadCustomers()', 1500)"/>
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
                                <button 
                                    type="button"
                                    class="close my-close"
                                    data-toggle="tooltip"
                                    title="Ayuda"
                                    onclick="helpMain('.toggle-customers', '#buttonAddCustomer', '#actionsCustomers')">
                                    <i class="fa fa-question-circle-o"></i>
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-success float-right element-hide" 
                                    id="buttonAddCustomer" onclick="openAddCustomer()">
                                    Agregar<i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"> 
                            <table id="customersTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Logo</th>
                                        <th>Razón social</th>
                                        <th class="text-center" id="actionsCustomers">Acciones</th>
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
    @include('customers.addModal')
    @include('customers.editModal')
    @include('customers.logosModal')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('customers.config_permissions')
@include('components.validate_js')
@include('customers.customers_js')
@include('components.help.helpModule')
@include('customers.helpModule')
@endsection
