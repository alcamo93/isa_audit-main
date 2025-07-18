@php
    $FINISHED_AUDIT = 10;
@endphp
@foreach ($corporateProcess as $e)
    <div class="col-sm-12 col-md-6 col-lg-3">
        <div class="card text-center">
            @php
                $tc = 'success';
                if( $e['total'] <= 50.00 ) $tc = 'danger';
                if( $e['total'] > 50.00  && $e['total'] <= 99.00 ) $tc = 'warning';
            @endphp
            <div class="card-header bg-{{ $tc }}">
                <h5 class="card-title text-white ">{{ $e['corp_tradename'] }}</h5>
                <h5 class="card-title text-white ">{{ $e['audit_processes'] }}</h5>
                @php 
                    $titleStatus = ($e['id_status_audit'] != $FINISHED_AUDIT) ? 'Avance de Auditoria' : 'Cumplimiento Global';
                    $totalStatus = ($e['id_status_audit'] != $FINISHED_AUDIT) ? $e['total_audit'] : $e['total'];
                @endphp
                <h5 class="card-title text-white"> {{ $titleStatus }} </h5>
                <h2 class="card-title text-white" style="font-size: 4rem;">{{ number_format($totalStatus, 0, '.', ',') }}%</h2>
                <hr>
            </div>
            <div class="card-body bg-white">        
                <span class="badge badge-success text-white">
                    Estatus: {{ $e['status_audit'] }}
                </span>
                <div class="sp-10"></div>
                @if ( $e['id_status_audit'] == 10 )
                    <span class="badge badge-info text-white">
                        Fecha de termino {{ $e['end_date'] }}
                    </span>
                @endif
                <div class="sp-10"></div>
                @if ( $e['id_status_audit'] == 10 ) 
                    <button class="btn btn-primary" onclick="showCustomerInfo({{ $e['id_audit_processes'] }}, {{ $e['id_action_register'] }})" >Mostrar información</button>
                @else
                    <button class="btn btn-primary" onclick="openAudit({{ $e['id_audit_register'] }})" >Ir a auditoría</button>
                @endif
            </div>
        </div>
    </div>
@endforeach