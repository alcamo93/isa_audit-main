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
        <div class="card" id="filterContract" >
            <div class="card-body" data-step="1" data-intro="Filtros para contratos" data-position='bottom' data-scrollTo='tooltip'>
                <div class="row">
                    <input type="hidden" name="filterIdCustomer" id="filterIdCustomer" value="{{ Session::get('profile')['id_customer'] }}">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Planta</label>
                            <select id="filterIdCorporate" name="filterIdCorporate" class="form-control"
                                onchange="reloadContracts()">
                                <option value="0">Todos</option>
                                @foreach($corporates as $element)
                                    <option value="{{ $element['id_corporate'] }}">{{ $element['corp_tradename'] }}</option>
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