@if(sizeof($dataObligations) > 0)
<div>
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card text-center toggle-seccion">
                <div class="card-body">
                    <button 
                        id="toggleObligations" 
                        class="btn btn-primary btn-fill btn-round btn-icon float-left" 
                        data-toggle="tooltip" data-original-title="Esconder obligaciones" 
                        >
                        <i class="fa fa-chevron-up text-white" style="width: 20px;" aria-hidden="true"></i>
                    </button>
                    <h6>Obligaciones</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row obligations">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card card-tasks" data-step="5" data-intro="Tabla de recordatorios" data-position='left' data-scrollTo='tooltip'>
                <div class="card-header ">
                    <p class="card-category">Las siguientes obigaciones son puntos para recordar hacer durante la fechas establecidas</p>
                    <a 
                        id="" 
                        class="btn btn-primary btn-fill float-right" 
                        data-toggle="tooltip" data-original-title="ir a obligaciones"
                        href="/obligations"
                        >
                        Obligaciones
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-full-width">
                        <table class="table table-striped text-center">
                            <thead>
                                <th>Obligación</th>
                                <th style="width: 100px; ">Fecha de cierre</th>
                            </thead>
                            <tbody>
                                @foreach ($dataObligations as $dataObligation)
                                    <?php 
                                        $oDateClass = 'warning';
                                        $hasRenewalDate = !is_null($dataObligation['renewal_date']);
                                        $oDate = ($hasRenewalDate) ? date('d-m-Y', strtotime($dataObligation['renewal_date'])) : '-';
                                        if (strtotime('today') > strtotime($dataObligation['renewal_date'])){ 
                                            $oDateClass = 'danger';
                                            $hasLastRenewalDate = !is_null($dataObligation['last_renewal_date']);
                                            $oDate = ($hasLastRenewalDate) ? date('d-m-Y', strtotime($dataObligation['last_renewal_date'])) : '-';
                                        }
                                    ?>
                                    <tr>
                                        <td>{{ $dataObligation['obligation'] }}</td>
                                        <td>
                                            <div class="text-{{ $oDateClass }}" >{{ $oDate }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif