@extends('theme.master')
@section('view')
    <div class="section-main">
        <div class="row">
            <div class="col">
                @php
                    $text = array( 
                        'title' => 'Área de filtrado', 
                        'tooltip' => ' area de filtrado', 
                        'idElement'=> '#filsterEvaluation', 
                        'idToggle' => 'evaluation' ); 
                @endphp
                @include('components.toggle.toggle', $text)
                <div class="card" id="filsterEvaluation">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <select id="filterIdCustomer" name="filterIdCustomer" class="form-control"
                                            onchange="setCorporates(this.value, '#filterIdCorporate', reloadProcesses)">
                                        @if( sizeof($customers) > 0 )
                                            <option value="0">Todos</option>
                                            @foreach($customers as $element)
                                                <option value="{{ $element['id_customer'] }}">{{ $element['cust_trademark'] }}</option>
                                            @endforeach
                                        @else
                                            <option value="0">No cuenta con clientes</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Planta</label>
                                    <select id="filterIdCorporate" name="filterIdCorporate" class="form-control"
                                            onchange="reloadProcesses()">
                                            <option value="0">Todos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label>Auditoría</label>
                                    <input class="form-control" type="text" 
                                        name="filterProcess" id="filterProcess"
                                        placeholder="Busqueda por nombre"
                                        onkeyup="typewatch('reloadProcesses()', 1500)"
                                    />
                                </div>
                            </div>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
        @include('processes.main.table_section')
    </div>
    @include('processes.modals.addModal')
    @include('processes.modals.editModal') 
    @include('processes.modals.showModal')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.toggle.toggle_js')
@include('components.validate_js')
@include('components.get_corporates_js')
@include('processes.audit_js')
@include('processes.main.main_js')
@include('components.help.helpModule')
@endsection