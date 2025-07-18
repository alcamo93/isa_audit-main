<div class="row">
    <div class="col">
        @php
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterContract', 
                'idToggle' => 'contract' ); 
        @endphp
        @include('components.toggle.toggle', $text)
        <div class="card" id="filterContract" >
            <div class="card-body">
                <div class="row">
                    <input type="hidden" name="filterIdCustomer" id="filterIdCustomer" value="{{ Session::get('profile')['id_customer'] }}">
                    <input type="hidden" name="filterIdCorporate" id="filterIdCorporate" value="{{ Session::get('profile')['id_corporate'] }}">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Auditoría</label>
                            <input class="form-control" type="text" 
                                name="filterProcess" id="filterProcess"
                                placeholder="Busqueda por nombre"
                                onkeyup="typewatch('reloadContracts()', 1500)"
                            />
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
                                <th>Fecha termino</th>
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