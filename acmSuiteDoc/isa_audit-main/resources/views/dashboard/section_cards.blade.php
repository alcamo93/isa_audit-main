<div class="row corporates"> 
    @if( isset($corporateProcess) )
        @include('dashboard.audit_card')
    @else
        @include('dashboard.no_audit_card')
    @endif
</div>
<div class="row">
    <div class="col {{ (count($dataNew) == 0) ? 'd-none' : '' }}" id="NewsSection">
        <div class="card toggle-seccion">
            <div class="card-body text-center">
                <button
                    id="toggleNews"
                    class="btn btn-primary btn-fill btn-round btn-icon float-left"
                    data-toggle="tooltip" data-original-title="Esconder noticias"
                    >
                    <i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>
                </button>
                <h6> Noticias </h6>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12 news">
                <div class="card">
                    <div class="card-body">
                        @include('dashboard.news')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>