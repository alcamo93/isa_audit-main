<div class="customer-selection">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-12">
            <div class="card text-center toggle-seccion">
                <div class="card-body">
                    <button 
                        id="toggleCPS" 
                        class="btn btn-primary btn-fill btn-round btn-icon float-left" 
                        data-toggle="tooltip" data-original-title="Ocultar sección de auditorías" 
                        >
                        <i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>
                    </button>
                    <h6>Auditorías</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row corporationPreviewSelection"></div>
    @include('dashboard.section_cards')
</div>