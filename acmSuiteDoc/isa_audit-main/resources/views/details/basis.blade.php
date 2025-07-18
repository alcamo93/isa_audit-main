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
            <div class="full-page register-page section-image" data-color="" >
                <!-- <div class="full-page register-page section-image" data-color="black" data-image="{{ asset('/assets/img/img_3bn.jpg') }}"> -->
                
                <div class="content" style="padding-top: 5vh !important;">
                    <div class="container">
                        <div class="card card-register card-plain text-center">
                            <div class="card-header ">
                                <div class="row justify-content-center">
                                    <div class="col-md-10">
                                        <div class="header-text">
                                            <h2 class="card-title text-dark font-weight-bold">Fundamentos Legales para:</h2>
                                            <h4 class="card-subtitle text-dark font-weight-bold">{{ $title }}</h4>
                                            <hr />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body ">
                            @foreach($basis as $e)
                                <div class="row">
                                    <div class="media">
                                        <div class="media-body text-dark font-weight-bold">
                                            <h5 class="font-weight-bold">{{ $e['guideline'] }}</h5>
                                            <h5 class="font-weight-bold">{{ $e['legal_basis']}}</h5>
                                            <div>{!! $e['legal_quote'] !!}</div>
                                        </div>
                                    </div>        
                                </div>
                                <hr class="divider">
                            @endforeach
                            </div>
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
    <script>
        $(document).ready( () => {
            checkFullPageBackgroundImage();
        });
        /**
         * Set background in login
         */
        function checkFullPageBackgroundImage() {
            $page = $('.full-page');
            image_src = $page.data('image');

            if (image_src !== undefined) {
                image_container = `<div class="full-page-background" style="background-image: url(${image_src})"/>`
                $page.append(image_container);
            }
        }
    </script>
</html> 

