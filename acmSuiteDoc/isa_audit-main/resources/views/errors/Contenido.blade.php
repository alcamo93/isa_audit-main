@extends('theme.master')
@section('view')
    <div class="row" style="height: calc(100vh - 180px);">
        <div class="col d-flex align-items-center">
            <div class="col-md-4 col-sm-6 ml-auto mr-auto">
                <img src="{{asset('assets/img/errorContenido.png')}}" alt="" class="img-fluid">
                <div class="card-body text-center">
                    <p style="color:#636363;"><span class="h4" style="font-weight: bold;">Contenido no disponible</span></p>
                    <p style="color:#636363;">Aún no hay auditorías finalizadas</p>
                    <a  href="{{asset('processes')}}" class="btn btn-primary btn-sm">Ir a Auditoría</a>
                </div>
            </div>
        </div>

    </div>
@endsection
