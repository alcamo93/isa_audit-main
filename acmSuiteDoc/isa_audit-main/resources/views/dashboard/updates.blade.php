<div class="col" id="NewsSection">
    <div class="card toggle-seccion">
        <div class="card-body text-center">
            <h6> Noticias </h6>
        </div>
    </div>
    @if(count($dataNew) > 0)
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12 news">
                <div class="card">
                    <div class="card-body">
                        @include('dashboard.news')
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12 news">
                <div class="card">
                    <div class="card-body">
                        No hay noticias al momento
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

    @if(count($dataLegal) == 0)
    <div class="col d-none" id="LegSection">
    @else
    <div class="col" id="NewsSection">
    @endif
        <div class="card toggle-seccion">
            <div class="card-body text-center">
                <h6> Actualizacion en legislación </h6>
            </div>
        </div>
        @if(count($dataLegal) > 0)
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12 news">
                    <div class="card">
                        <div class="card-body">
                        @foreach( $dataLegal as $dataLegals)
                            <li class="list-group-item">
                                <p style="font-size:18px;padding:0px;" class="font-weight-bold text-justify">
                                    <?php 
                                    print_r($dataLegals['legal_basis']);
                                    echo "</p><br>";
                                    ?>
                                    <p style="font-size:15px;padding:0px;" class="text-justify"><?php
                                    print_r($dataLegals['guideline']);
                                    echo "</p><br>";
                                    ?> 
                                    <p style="font-size:12px;" class="text-right font-weight-light font-italic"><?php
                                    print_r($dataLegals['update_format']);
                                    ?>
                                    </p>
                                </li>
                                @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>