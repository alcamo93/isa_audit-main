<div class="col-sm-12 col-md-6 col-lg-4">
    <div class="form-group">
        <label>Cliente</label>
        <div>{{ Session::get('customer')['cust_trademark'] }}</div>
        <input 
            type="hidden"
            id="filterIdCustomer" 
            value="{{ Session::get('customer')['id_customer'] }}"
            >
    </div>
</div>
<div class="col-sm-12 col-md-6 col-lg-4">
    <div class="form-group">
        <label>Planta</label>
        <div>{{ Session::get('corporate')['corp_tradename'] }}</div>
        <input 
            type="hidden"
            id="filterIdCorporate" 
            value="{{ Session::get('corporate')['id_corporate'] }}"
            >
    </div>
</div>
<div class="col-sm-12 col-md-12 col-lg-4">
    <div class="form-group">
        <label>Perfil</label>
        <select id="filterIdProfile" name="filterIdProfile" class="form-control"
            onchange="reloadPermission()">
                <option selected value="0">Seleciona uno</option>
                @foreach($profiles as $element)
                    <option value="{{ $element['id_profile'] }}">{{ $element['profile_name'] }}</option>
                @endforeach
        </select>
    </div>
</div>