<div class="row">    
    <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="form-group">
            <label>Cliente<span class="star">*</span></label>
            <select id="{{ $word }}idCustomer" name="{{ $word }}idCustomer" class="form-control"
                    onchange="setCorporates(this.value, '#{{ $word }}idCorporate')"
                    title="Selecciona una opción" required>
                <option value=""></option>
                @foreach($customers as $element)
                    <option value="{{ $element['id_customer'] }}">{{ $element['cust_trademark'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="form-group">
            <label>Planta<span class="star">*</span></label>
            <select 
                id="{{ $word }}idCorporate"
                name="{{ $word }}idCorporate"
                class="form-control"
                title="Selecciona una opción"
                onchange="setProfilesSelector('#{{ $word }}idCorporate', '#{{ $word }}idProfile')"
                required
                >
                <option value=""></option>
            </select>
        </div>
    </div>
</div>