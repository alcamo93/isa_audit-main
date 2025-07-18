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
            <div class="full-page lock-page" data-color="black" data-image="{{ asset('assets/img/img_3bn.jpg') }}">
                <div class="content">
                    <div class="container">

                        <div class="col-md-4 col-sm-6 ml-auto mr-auto">
                            <form class="form" id="setResetForm" method="POST" action="{{ asset('/login/setReset') }}">>
                                {!! csrf_field() !!}
                                <div class="card card-login card-hidden">
                                    <div class="card-header ml-auto mr-auto">
                                        <img src="{{ asset('logos/acm_suite_logo_h.png') }}" alt="" class="img-fluid">
                                    </div>

                                        <div class="card-body">
                                            <div class="form-group has-label">
                                                <label>Nueva Contraseña<span class="star">*</span></label>
                                                <input type="password" id="newPassword" name="newPassword" placeholder="Escribe tu nueva contraseña" class="form-control"
                                                    data-rule-required="true" data-msg-required="Es necesario escribas tu nueva contraseña"
                                                    data-rule-minlength="8" data-msg-minlength="Mínimo 8 caracteres">
                                            </div>
                                            <div class="form-group has-label">
                                                <label>Repetir Contraseña<span class="star">*</span></label>
                                                <input type="password" id="repitPassword" name="repitPassword" placeholder="Repite tu contraseña" class="form-control"
                                                    data-rule-required="true" data-msg-required="Es necesario que repitas la contraseña"
                                                    data-rule-minlength="8" data-msg-minlength="Mínimo 8 caracteres"
                                                    data-rule-equalTo="#newPassword" data-msg-equalTo="Las contraseñas no coincide">
                                            </div>
                                        </div>

                                    <div class="card-footer ml-auto mr-auto">
                                        <button type="submit" class="btn btn-primary btn-wd d-block p-1"><i class="fa fa-sign-in"></i> Reestablecer</button>
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
    @include('auth.reset_js')
</html> 

