<!doctype html>
<html lang="en"> 
    <head>
        {{-- Meta --}}
            @section('meta')
                @include('theme.components.meta')
            @show
        {{-- CSS --}}
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="wrapper wrapper-full-page">
            <div class="full-page lock-page" data-color="black" data-image="assets/img/img_3bn.jpg">
                <div class="content">
                    <div class="container">
                        <div id="login_card" class="col-md-4 col-sm-6 ml-auto mr-auto">
                            <form class="form" id="loginForm" method="POST" action="{{ asset('/login') }}">
                                {!! csrf_field() !!}
                                <div class="card card-login card-hidden">
                                    <div class="card-header ml-auto mr-auto">
                                        <img src="logos/acm_suite_logo_h.png" alt="" class="img-fluid">
                                    </div>

                                        <div class="card-body">
                                            <div class="form-group has-label">
                                                <label>Correo electrónico</label>
                                                <input type="email" name="email" id="email" placeholder="Escribe tu correo" class="form-control"
                                                    data-rule-required="true" data-msg-required="Escribe un correo"
                                                    data-rule-email="true" data-msg-email="El formato de correo es incorrecto">
                                            </div>
                                            <div class="form-group has-label">
                                                <label>Contraseña</label>
                                                <input type="password" name="password" id="password" placeholder="Contraseña" class="form-control"
                                                    data-rule-required="true" data-msg-required="Es necesaria una contraseña"
                                                    data-rule-minlength="8" data-msg-minlength="Mínimo 8 caracteres">
                                            </div>
                                            <div class="form-group has-label">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" id="remember" name="remember" type="checkbox">
                                                        <span class="form-check-sign"></span>
                                                        Recordar sesión
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                    <div class="card-footer ml-auto mr-auto">
                                        <button type="submit" class="btn btn-primary btn-wd d-block p-1"><i class="fa fa-sign-in"></i> Entrar</button>
                                        <a class="badge badge-light d-block my-cursor" id="show_password_recovery">¿Olvidaste tu contraseña?</a>
                                    </div> 
                                </div>
                            </form>
                        </div>

                        <div id="forget_card"class="col-md-4 col-sm-6 ml-auto mr-auto element-hide">
                            <form class="form" id="recoveryForm" method="POST" action="{{ asset('/login/reset') }}">
                                <div class="card card-login card-hidden">
                                    <div class="card-header ml-auto mr-auto">
                                        <img src="logos/acm_suite_logo_h.png" alt="" class="img-fluid">
                                    </div>

                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Correo electrónico</label>
                                                <input type="email" id="emailRecovery" name="emailRecovery" placeholder="Escribe tu correo" class="form-control"
                                                    data-rule-required="true" data-msg-required="Escribe algún correo relacionado a tu cuenta"
                                                    data-rule-email="true" data-msg-email="El formato de correo es incorrecto">
                                            </div>
                                        </div>

                                    <div class="card-footer ml-auto mr-auto">
                                        <button type="submit" class="btn btn-primary btn-wd d-block p-1"><i class="fa fa-key"></i> Recuperar</button>
                                        <a class="badge badge-light d-block my-cursor" id="show_login">Volver a acceso</a>
                                    </div> 
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
            {{-- Footer --}}
            @include('theme.components.footer')
        </div>
        
    </body>
    {{-- JavaScript --}}
    @section('javascript')
        @include('theme.components.javascript')
    @show
    @include('components.components_js')
    @include('components.validate_js')
    @include('auth.login_js')
</html> 

