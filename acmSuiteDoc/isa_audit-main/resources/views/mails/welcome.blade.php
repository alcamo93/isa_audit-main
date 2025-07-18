@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://www.acmsuite.com/'])
            <img style="max-width: 8em !important;" src="{{ asset('/logos/acm_suite_logo_h.png') }}">
        @endcomponent
    @endslot

    {{-- Body --}}
    
    Hola <b>{{ (isset($data['name']) ? $data['name'] : '') }}</b>, gracias por unirte a <b>ACM Suite</b>. Tu cuenta ya esta lista.

    Datos de acceso:
        * Usuario: {{ (isset($data['user']) ? $data['user'] : '') }}
        * Contraseña: {{ (isset($data['password']) ? $data['password'] : '') }}

@component('mail::button', ['url' => asset('/')])
Acceso
@endcomponent

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} <b>ACM Suite</b>. Todos los derechos reservados.
        @endcomponent
    @endslot
@endcomponent

