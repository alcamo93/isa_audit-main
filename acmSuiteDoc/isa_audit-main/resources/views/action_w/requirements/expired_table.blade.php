<div class="row">
    <div class="col">
        @php 
            $text = array( 
                'title' => 'Requerimientos Vencidos', 
                'tooltip' => ' Requerimientos Vencidos', 
                'idElement'=> '#areaExpired', 
                'idToggle' => 'matter-aspect' ); 
        @endphp
        @include('components.toggle.toggle', $text)
        <div class="card" id="areaExpired">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="expiredTable" class="table table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="text-center">Requerimiento</th>
                                <th class="text-center">Prioridad</th>
                                <th class="text-center">Hallazgo</th>
                                <th class="text-center">Causa de Desviación</th>
                                <th class="text-center">Fecha de Resolución</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center">Responsable de cierre</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
</div>