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
            <div id="app" class="full-page register-page section-image" data-color="" >
              <details-page 
                items="{{ $data['items'] }}"
                data="{{ $data['data'] }}"
              />
            </div>
            {{-- Footer --}}
            @include('theme.components.footer')
        </div>
    </body>
    {{-- JavaScript --}}
    @section('javascript')
      <script src="{{ mix('js/app.js') }}"></script>
    @show
</html> 