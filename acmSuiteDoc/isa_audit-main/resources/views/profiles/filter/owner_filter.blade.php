<div class="col-sm-12 col-md-6 col-lg-6">
    <div class="form-group">
        <label>Cliente</label>
        <select id="filterIdCustomer" name="filterIdCustomer" class="form-control"
                onchange="setCorporates(this.value, '#filterIdCorporate', reloadProfiles)">
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
                onchange="reloadProfiles()">
            <option value="0">Todos</option>
        </select>
    </div>
</div>