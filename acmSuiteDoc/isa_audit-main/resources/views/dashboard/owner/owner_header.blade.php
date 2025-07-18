<div class="customer-selection">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card text-center toggle-seccion">
                <div class="card-body">
                    <button
                        id="toggleCPS"
                        class="btn btn-primary btn-fill btn-round btn-icon float-left"
                        data-toggle="tooltip" data-original-title="Esconder selección de clientes" >
                        <i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>
                    </button>
                    <h6>Visión de clientes</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row corporationPreviewSelection">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <label>Selección de Cliente</label>
                    <select id="filterIdCustomer" name="filterIdCustomer" class="form-control"
                            onchange="getContracts(this.value)">
                        <option value="0">Todos</option>
                        @foreach($customers as $element) 
                            <option value="{{ $element['id_customer'] }}">{{ $element['cust_trademark'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body">
                    <h6 id="customers-title" class="text-center">Auditorias Disponibles</h6>
                </div>
            </div>
        </div>
    </div>
    @include('dashboard.section_cards')
</div>
