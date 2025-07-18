<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="form-group">
            <label>N° de requerimiento</label>
            <input id="filterRequirementNumber" name="filterRequirementNumber" type="text" class="form-control" 
                placeholder="Filtar por número" onkeyup="typewatch('reloadRequirements()', 1500)"/>
        </div>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="form-group">
            <label>Requerimiento</label>
            <input id="filterRequirement" name="filterRequirement" type="text" class="form-control" 
                placeholder="Filtar por requerimiento" onkeyup="typewatch('reloadRequirements()', 1500)"/>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <div class="form-group">
            <label>Materia</label>
            <select id="filterIdMatter" name="filterIdMatter" class="form-control"
                    onchange="setAspects(this.value, '#filterIdAspect', 'Todos', true)">
                <option value="">Todos</option>
                @foreach($matters as $element)
                    <option value="{{ $element['id_matter'] }}">{{ $element['matter'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-3">
        <div class="form-group">
            <label>Aspecto</label>
            <select id="filterIdAspect" name="filterIdAspect" class="form-control"
                onchange="reloadRequirements()">
                <option value="">Todos</option>
            </select>
        </div>
    </div>
    <div class="d-none col-sm-12 col-md-6 col-lg-3">
        <div class="form-group">
            <label>Periodo de cierre</label>
            <select id="filterIdObtainingPeriod" name="filterIdObtainingPeriod" class="form-control"
                onchange="reloadRequirements()">
                <option value="">Todos</option>
                @foreach($periods as $element)
                    <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="d-none col-sm-12 col-md-6 col-lg-3">
        <div class="form-group">
            <label>Periodo de actualización</label>
            <select id="filterIdUpdatePeriod" name="filterIdUpdatePeriod" class="form-control"
                onchange="reloadRequirements()">
                <option value="">Todos</option>
                @foreach($periods as $element)
                    <option value="{{ $element['id_period'] }}">{{ $element['period'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="d-none col-sm-12 col-md-6 col-lg-6">
        <div class="form-group">
            <label>Evidencia</label>
            <select id="filterIdEvidence" name="filterIdEvidence" class="form-control"
                onchange="reloadRequirements()">
                <option value="">Todos</option>
                @foreach($evidences as $element)
                    <option value="{{ $element['id_evidence'] }}">{{ $element['evidence'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="form-group">
            <label>Tipo de aplicación</label>
            <select id="filterIdAplicationType" name="filterIdAplicationType" class="form-control"
                onchange="reloadRequirements()">
                <option value="">Todos</option>
                @foreach($appTypes as $element)
                    <option value="{{ $element['id_application_type'] }}">{{ $element['application_type'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-sm-12 col-md-6 col-lg-6"> 
        <div class="form-group">
            <label>Estado</label>
            <select id="filterIdState" name="filterIdState" class="form-control"
                onchange="setCities(this.value, '#filterIdCity', reloadRequirements)">
                <option value="">Todos</option>
                @foreach($states as $element)
                    <option value="{{ $element['id_state'] }}">{{ $element['state'] }}</option>
                @endforeach
            </select> 
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-6"> 
        <div class="form-group">
            <label>Ciudad</label>
            <select id="filterIdCity" name="filterIdCity" class="form-control"
                onchange="reloadRequirements()">
            </select> 
        </div>
    </div>
</div>
<div class="row d-none" id="areaFilterCustomer">
    <div class="col-sm-12 col-md-6 col-lg-6">
        <div class="form-group">
            <label>Cliente</label>
            <select id="filterIdCustomer" name="filterIdCustomer" class="form-control"
                    onchange="setCorporates(this.value, '#filterIdCorporate', reloadRequirements)">
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
                onchange="reloadRequirements()">
                <option value="0">Todos</option>
            </select>
        </div>
    </div>
</div>