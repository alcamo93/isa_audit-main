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
        <label>Planta</label>
        <div class="text-center">{{ $corporates[0]['corp_tradename'] }}</div>
        <input
            id="{{ $prefix }}idCorporate"
            name="{{ $prefix }}idCorporate"
            class="form-control d-none"
            value="{{ $corporates[0]['id_corporate'] }}"
        >
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
            @foreach($process as $p)
                <option value="{{ $p['id_audit_processes'] }}">{{ $p['audit_processes'] }}</option>
            @endforeach
        </select>
    </div>
</div>