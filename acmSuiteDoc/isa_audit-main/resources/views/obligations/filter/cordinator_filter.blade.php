<div class="col-sm-12 col-md-6 col-lg-4">
    <div class="form-group">
        <label>Cliente</label>
        <div class="text-center">{{ $customers[0]['cust_trademark'] }}</div>
        <input 
            id="{{ $prefix }}idCustomer" 
            name="{{ $prefix }}idCustomer" 
            class="form-control d-none"
            value="{{ $customers[0]['id_customer'] }}"
        />
    </div>
</div>
<div class="col-sm-12 col-md-6 col-lg-4">
    <div class="form-group">
        <label>
            Planta
            @if($required == 'true')
            <span class="star">*</span>
            @endif
        </label>
        <select id="{{ $prefix }}idCorporate" name="{{ $prefix }}idCorporate" class="form-control"
                onchange="setAuditProcess(this.value, '#{{ $prefix }}idAuditProcess' {{ ($required == 'true') ? '' : ', reloadObligations' }} )">
            <option value="{{ ($required == 'true') ? '' : 0 }}">{{ ($required == 'true') ? '' : 'Todos' }}</option>
            @foreach($corporates as $element)
                <option value="{{ $element['id_corporate'] }}">{{ $element['corp_tradename'] }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-sm-12 col-md-12 col-lg-4">
    <div class="form-group">
        <label>
            Auditoría
            @if($required == 'true')
            <span class="star">*</span>
            @endif
        </label>
        <select id="{{ $prefix }}idAuditProcess" name="{{ $prefix }}idAuditProcess" class="form-control"
                data-rule-required="{{ $required }}" data-msg-required="Este campo es obligatorio"
                onchange="{{ ($required == 'true') ? 'void(0)' : 'reloadObligations()' }}">
            <option value="{{ ($required == 'true') ? '' : 0 }}">{{ ($required == 'true') ? '' : 'Todos' }}</option>
        </select>
    </div>
</div>