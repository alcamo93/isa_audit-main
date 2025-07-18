<div class="row">
    <div class="col">
        <?php 
            $text = array( 
                'title' => 'Área de filtrado', 
                'tooltip' => ' area de filtrado', 
                'idElement'=> '#filterAreaRequirements', 
                'idToggle' => 'requirements' ); 
        ?>
        @include('components.toggle.toggle', $text)
        <div class="card gone" id="filterAreaRequirements" style="display: none;" >
            <div class="card-body">
                <div class="row" id="areaFilterCustomer">
                    @switch(Session::get('profile')['id_profile_type'])
                        @case(1) @case(2) 
                            @include('requirements.requirements.filter.owner_filter') 
                        @break
                        @case(3) @case(4) @case(5) 
                            @include('requirements.requirements.filter.customer_filter') 
                        @break
                    @endswitch
                </div>
                @include('requirements.requirements.requirements_filter')
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <button 
                            type="button" 
                            class="close my-close" 
                            data-toggle="tooltip" 
                            title="Ayuda" 
                            onclick="helpMain('.toggle-requirements', '#buttonAddRequirement', '#actionsRequirements')"
                            >
                            <i class="fa fa-question-circle-o"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-success float-right element-hide" 
                            id="buttonAddRequirement" onclick="openRequirementModel(null, false, false)">
                            Agregar<i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"> 
                    <table id="requirementsTable" class="table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>N° de requerimiento</th>
                                <th>Requerimiento</th>
                                <th>Subrequerimientos</th>
                                <th class="text-center" id="actionsRequirements">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>