<div class="col-sm-12 col-md-6 col-lg-4">
    <div class="form-group">
        <label>
            Cliente
            @if($required == 'true')
            <span class="star">*</span>
            @endif
        </label>
        <select id="{{ $prefix }}idCustomer" name="{{ $prefix }}idCustomer" class="form-control"
            onchange="setCorporates(this.value, '#{{ $prefix }}idCorporate' {{ ($required == 'true') ? '' : ', reloadObligations' }} )"
            data-rule-required="{{ $required }}" data-msg-required="Este campo es obligatorio">
            <option value="{{ ($required == 'true') ? '' : 0 }}">{{ ($required == 'true') ? '' : 'Todos' }}</option>
            @foreach($customers as $element)
                <option value="{{ $element['id_customer'] }}">{{ $element['cust_trademark'] }}</option>
            @endforeach
        </select>
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
            data-rule-required="{{ $required }}" data-msg-required="Este campo es obligatorio"    
            onchange="setAuditProcess(this.value, '#{{ $prefix }}idAuditProcess' {{ ($required == 'true') ? '' : ', reloadObligations' }})">
            <option value="{{ ($required == 'true') ? '' : 0 }}">{{ ($required == 'true') ? '' : 'Todos' }}</option>
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