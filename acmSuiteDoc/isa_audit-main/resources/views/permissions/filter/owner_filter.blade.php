<div class="col-sm-12 col-md-6 col-lg-4">
    <div class="form-group">
        <label>Cliente</label>
        <select id="filterIdCustomer" name="filterIdCustomer" class="form-control"
                onchange="setCorporates(this.value, '#filterIdCorporate', reloadPermission)">
            <option value="0">Seleciona uno</option>
            @foreach($customers as $element)
                <option value="{{ $element['id_customer'] }}">{{ $element['cust_trademark'] }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-sm-12 col-md-6 col-lg-4">
    <div class="form-group">
        <label>Planta</label>
        <select id="filterIdCorporate" name="filterIdCorporate" class="form-control"
            onchange="setProfiles(this.value, '#filterIdProfile', reloadPermission)">
            <option value="0">Seleciona uno</option>
        </select>
    </div>
</div>
<div class="col-sm-12 col-md-12 col-lg-4">
    <div class="form-group">
        <label>Perfil</label>
        <select id="filterIdProfile" name="filterIdProfile" class="form-control"
            onchange="reloadPermission()">
                <option selected value="0">Seleciona uno</option>
        </select>
    </div>
</div>