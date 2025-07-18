<div class="row">   
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="form-group">
            <input
                id="{{ $word }}idCustomer"
                name="{{ $word }}idCustomer" 
                type="hidden"
                value="{{ $idCustomer }}">
            <label>Planta<span class="star">*</span></label>
            <select 
                id="{{ $word }}idCorporate"
                name="{{ $word }}idCorporate"
                class="form-control"
                title="Selecciona una opción"
                onchange="getLeaders(this.value, '#{{ $word }}idLeader', '#{{ $word }}auditors')"
                required
                >
                <option value=""></option>
                @foreach($corporates as $element)
                    <option value="{{ $element['id_corporate'] }}">{{ $element['corp_tradename'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
