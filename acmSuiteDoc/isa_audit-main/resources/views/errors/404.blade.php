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
            <div class="full-page section-image" data-color="black" data-image="{{asset('assets/img/img_3bn.jpg')}}"">
                <div class="content">
                    <div class="container">
                        <div class="col-md-4 col-sm-6 ml-auto mr-auto">
                            <div class="card card-login card-hidden">
                                <div class="card-header ml-auto mr-auto">
                                    <img src="{{asset('logos/acm_suite_logo_h.png')}}"" alt="" class="img-fluid">
                                </div>
                                <div class="card-body text-center">
                                    <h1 class=""> 404</h1>
                                    <h5><i class="fa fa-warning pr-2"></i>Ups! Algo salio mal</h5>
                                    <p>La página que esta buscando no está disponible, por favor intente más tarde</p>
                                    <a  href="{{asset('/v2/dashboard/customers/view')}}" class="btn btn-primary btn-sm">Ir a Tablero de control</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </body>
    {{-- JavaScript --}}
    @section('javascript')
        @include('theme.components.javascript')
    @show
    <script>
        $(document).ready(function() {
            login.checkFullPageBackgroundImage();
            setTimeout(function() {
                // after 1000 ms we add the class animated to the login/register card
                $('.card').removeClass('card-hidden');
            }, 700)
        });
        login = {
            //set background in login
            checkFullPageBackgroundImage: function() {
                $page = $('.full-page');
                image_src = $page.data('image');

                if (image_src !== undefined) {
                    image_container = `<div class="full-page-background" style="background-image: url(${image_src})"/>`
                    $page.append(image_container);
                }
            }
        }
    </script>
</html>