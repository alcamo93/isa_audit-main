@extends('theme.master')
@section('view')
<div class="row mt-10">
    <div class="col">
        <div class="card card-user">
            <div class="card-header">
                <div class="author">
                    <img class="avatar border-gray" id="imgAccount" src="{{ asset('/assets/img/faces/'.Session::get('user')['picture']) }}" 
                    data-toggle="tooltip" title="{{ Session::get('user')['complete_name'] }}" alt="...">
                </div>
            </div>
            <div class="card-body">
                
                <ul role="tablist" class="nav nav-tabs">
                    <li role="presentation" class="nav-item show active">
                        <a class="nav-link" id="unreadTab" href="#linkUnreadTab" data-toggle="tab"><i class="fa fa-bell"></i> No Leidas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="readTab" href="#linkReadTab" data-toggle="tab"><i class="fa fa-bell-slash"></i> Leidas</a>
                    </li>
                </ul>
                <div class="tab-content">

                    <div id="linkUnreadTab" class="tab-pane fade show active" role="tabpanel" aria-labelledby="unreadTab">
                        <div class="col">
                            <div class="table-responsive">
                                <table id="unreadNotifications" class="table table-striped table-hover" cellspacing="0" width="100%">
                                    <!-- <caption class="text-center font-weight-bold table-active" style="caption-side: top !important">No leidas</caption> -->
                                    <thead>
                                        <tr>
                                            <th class="text-justify"></th>
                                            <th class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="linkReadTab" class="tab-pane fade" role="tabpanel" aria-labelledby="readTab">
                        <div class="col">
                            <div class="table-responsive">
                                <table id="readNotifications" class="table table-striped table-hover" cellspacing="0" width="100%">
                                    <!-- <caption class="text-center font-weight-bold table-active" style="caption-side: top !important">Leidas</caption> -->
                                    <thead>
                                        <tr>
                                            <th class="text-justify"></th>
                                            <th class="text-center"></th>
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
        </div>
    </div>
</div>
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.validate_js')
@include('notifications.main_js')
@endsection