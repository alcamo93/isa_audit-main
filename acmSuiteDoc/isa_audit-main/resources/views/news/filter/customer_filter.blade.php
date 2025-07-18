<div class="col-sm-12 col-md-6 col-lg-6">
    <div class="form-group">
        <label>Cliente</label>
        <div class="text-center">{{ $customers[0]['cust_trademark'] }}</div>
        <input 
            id="filterIdCustomer" 
            name="filterIdCustomer" 
            class="form-control d-none"
            value="{{ $customers[0]['id_customer'] }}"
        />
    </div>
</div>
<div class="col-sm-12 col-md-6 col-lg-6">
    <div class="form-group">
        <label>Planta</label>
        <select id="filterIdCorporate" name="filterIdCorporate" class="form-control"
                onchange="reloadProfiles()">
            <option value="0">Todos</option>
            @foreach($corporates as $element)
                <option value="{{ $element['id_corporate'] }}">{{ $element['corp_tradename'] }}</option>
            @endforeach
        </select>
    </div>
</div>