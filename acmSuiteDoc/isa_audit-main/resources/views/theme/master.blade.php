<!DOCTYPE html>
<html lang="en">
    <head>
        {{-- Meta --}}
        @section('meta')
            @include('theme.components.meta')
        @show
        {{-- CSS --}}
        @section('css')
            @include('theme.components.css')
        @show
    </head>
    <body class="sidebar-mini">
        {{-- Vue Secction #app --}}
        <div id="app" class="wrapper">
            {{-- Sidebar --}}
            @include('theme.components.sidebar')
            {{-- content space --}}
            <div class="main-panel">
                {{-- Header --}}
                @include('theme.components.header')
                {{-- Page view --}} 
                <div class="content">
                    <div class="container-fluid">
                        @section('view')
                        @show
                    </div>
                </div>
                {{-- Footer --}}
                @include('theme.components.footer')
            </div>
        </div>
        {{-- JavaScript --}}
        @section('javascript')
            @auth
            {{-- Vue - Pusher - Laravel Echo --}}
            <script src="{{ mix('js/app.js') }}"></script>
            @endauth
            @include('theme.components.javascript')
        @show
    </body>
</html>
