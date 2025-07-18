<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterContract', 
                'idToggle' => 'contract' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card" id="filterContract">
            <div class="card-body" data-step="1" data-intro="Filtros para contratos" data-position='bottom' data-scrollTo='tooltip'>
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
                            <select id="filterIdCorporate" name="filterIdCorporate" class="form-control"
                                onchange="reloadContracts()">
                                <option value="0">Todos</option>
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
            <div class="card-body">
                <div class="table-responsive">
                    <table id="contractsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Planta</th>
                                <th>Auditoria</th>
                                <th class="text-center">Acciones</th>
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