<div class="row">
    <div class="col">
        @php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterArearMatterAspect', 
                'idToggle' => 'matter-aspect' ); 
        @endphp
        @include('components.toggle.toggle', $text)
        <div class="card" id="filterArearMatterAspect">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Cliente</label>
                            <div class="customerTitle"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Planta</label>
                            <div class="corporateTitle"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Auditoría</label>
                            <div class="auditTitle"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Alcance</label>
                            <div class="scopeTitle"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>Materia</label>
                            <select id="filterIdMatter" name="filterIdMatter" class="form-control"
                                    onchange="setAspects(this.value, '#filterIdAspect', reloadTables)">
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>Aspecto</label>
                            <select id="filterIdAspect" name="filterIdAspect" class="form-control"
                                onchange="reloadTables()">
                                <option value="0">Todos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>Requerimiento</label>
                            <input type="text" name="filterRequirement" 
                                id="filterRequirement" class="form-control"
                                onkeyup="typewatch('reloadTables()', 1500)"
                            >
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>Estatus</label>
                            <select id="filterIdStatus" name="filterIdStatus" class="form-control"
                                    onchange="reloadTables()">
                                <option value="0">Todos</option>
                                @foreach($statusAP as $s)
                                    <option value="{{ $s['id_status'] }}">{{ $s['status'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="form-group">
                            <label>Prioridad</label>
                            <select id="filterIdPriority" name="filterIdPriority" class="form-control"
                                onchange="reloadTables()">
                                <option value="0">Todos</option>
                                @foreach($priorities as $p)
                                    <option value="{{ $p['id_priority'] }}">{{ $p['priority'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="form-group">
                            <label>Rango de fechas</label>
                            <div class="input-group mb-3">
                                <input id="filterDates" type="text" 
                                    class="form-control" placeholder="Fecha de Inicio a Fecha de Cierre"
                                    autocomplete="off" style="background-color: #ffffff !important;"
                                />
                                <div class="input-group-prepend">
                                    <button class="btn btn-danger btn-xs" id="btnRefreshDates" 
                                        style="border-radius: 0px 3px 3px 0px !important;"
                                        data-toggle="tooltip" title="Limpiar Fechas" type="button">
                                        <i class="fa fa-refresh la-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="form-group">
                            <label>Responsable de cierre</label>
                            <input type="text" name="filterUserName" 
                                id="filterUserName" class="form-control"
                                onkeyup="typewatch('reloadTables()', 1500)"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>