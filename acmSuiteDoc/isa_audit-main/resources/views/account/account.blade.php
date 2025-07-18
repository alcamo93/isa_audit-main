@extends('theme.master')
@section('view')
    <div id="view">
        <div class="row mt-10">
            <div class="col">
                <div class="card card-user">
                    <div class="card-header">
                        <div class="author">
                            <a href="javascript:void(0)" onclick="choosePic()" data-toggle="tooltip" title="Cambiar foto">
                                <img class="avatar border-gray" id="imgAccount" src="{{ asset('/assets/img/faces/default.png') }}" 
                                data-toggle="tooltip" title="{{ Session::get('user')['complete_name'] }}" alt="...">
                                <!-- <img class="avatar border-gray" id="imgAccount" src="{{ asset('/assets/img/faces/'.Session::get('user')['picture']) }}" alt="..."> -->
                                <input id="accountPic" onchange="openCropModal(this, '/users/img', 1, $('#idUser').val())" class="element-hide" type="file" name="name" />
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form" id="setDataUser" method="POST" action="{{ asset('/account/set') }}">
                            <input type="hidden" id="idUser" name="idUser">
                            <input type="hidden" id="idPerson" name="idPerson">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Planta</label>
                                        <input type="text" class="form-control" id="corporate" name="corporate" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Correo electrónico</label>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="ejemplo@mail.com">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Correo electrónico secundario</label>
                                        <input type="email" id="secondaryEmail" name="secondaryEmail" class="form-control" placeholder="ejemplo@mail.com">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-1 col-md-4">
                                    <div class="form-group">
                                        <label>Nombre/s</label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="Nombre"
                                            data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres">
                                    </div>
                                </div>
                                <div class="col-sm-1 col-md-4">
                                    <div class="form-group">
                                        <label>Primer apellido</label>
                                        <input type="text" id="secondName" name="secondName" class="form-control" placeholder="Apellido paterno"
                                            data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres">
                                    </div>
                                </div>
                                <div class="col-sm-1 col-md-4">
                                    <div class="form-group">
                                        <label>Segundo apellido</label>
                                        <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Apellido materno"
                                            data-rule-maxlength="100" data-msg-maxlength="Máximo 100 caracteres">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-1 col-md-6">
                                    <div class="form-group">
                                        <label>RFC</label>
                                        <input type="text" class="form-control" id="rfc" name="rfc" placeholder="Introduce 13 dígitos"
                                            data-rule-maxlength="13" data-msg-maxlength="Máximo 13 caracteres">
                                    </div>
                                </div>
                                <div class="col-sm-1 col-md-6">
                                    <div class="form-group">
                                        <label>Perfil</label>
                                        <input type="text" disabled class="form-control" id="profile" name="profile">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-1 col-md-4">
                                    <div class="form-group">
                                        <label>Género</label>
                                        <select id="gender" name="gender" class="form-control">
                                            <option value=""></option>
                                            <option value="Femenino">Femenino</option>
                                            <option value="Masculino">Masculino</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1 col-md-4">
                                    <div class="form-group">
                                        <label>Celular</label>
                                        <input type="text" id="cell" name="cell" class="form-control" placeholder="Apellido paterno"
                                            data-rule-number="true" data-msg-number="Solo se permiten números"
                                            data-rule-minlength="10" data-msg-minlength="Máximo 10 caracteres"
                                            data-rule-maxlength="16" data-msg-maxlength="Máximo 16 caracteres">
                                    </div>
                                </div>
                                <div class="col-sm-1 col-md-4">
                                    <div class="form-group">
                                        <label>Fecha de nacimiento</label>
                                        <input id="birthdate" name="birthdate" type="date" class="form-control" placeholder="dd/MM/AAAA"/>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-info btn-fill pull-right">Actualizar perfil</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('account.profileModal')
    @include('components.loading')
@endsection
@section('javascript')
@parent
@include('components.components_js')
@include('components.validate_js')
@include('account.account_js')
@include('components.validate_img_js')
@endsection