<div class="row toggle-{{ $idToggle }}">
    <div class="col-12 col-md-12 col-lg-12">
        <div class="card text-center toggle-seccion">
            <div class="card-body">
                <button 
                    id="toggle-{{ $idToggle }}" 
                    class="btn btn-primary btn-fill btn-round btn-icon float-left" 
                    data-toggle="tooltip" data-original-title="Mostrar {{ $tooltip }}"
                    onclick="toggleArea('#toggle-{{ $idToggle }}', '{{ $idElement }}', '{{ $tooltip }}')"
                    >
                    <i class="fa fa-chevron-down  text-white" style="width: 20px;" aria-hidden="true"></i>
                </button>
                <h6>{{ $title }}</h6>
            </div>
        </div>
    </div>
</div>