<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="{{ auth()->check() ? auth()->user()->role : '' }}">

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Site Metas -->
    <title>{{ config('app.name') }} - @yield('title')</title>

    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Site Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo1.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo1.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo1.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />

    <!-- CSS locaux -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/carouselEffet.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table-style.css') }}">
    {{-- icon --}}
    <link rel="shortcut icon" href="{{ asset('images/logo1.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo1.png') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    {{-- Navbar --}}
    @include('Navbar.navbar')

    {{-- Contenu page --}}
    @yield('content')

    {{-- Footer --}}
    @include('Navbar.footer')

    {{-- Modal Édition Profil --}}
    @auth
        @include('components.profile_modal')
    @endauth

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- intl-tel-input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>
    <script src="{{ asset('js/phone-validation.js') }}"></script>

    <!-- Bootsnav JS -->
    <script src="{{ asset('js/bootsnav.js') }}"></script>
    <!-- Scripts globaux pour panier et infos client (disponibles sur toutes les pages) -->
    <script src="{{ asset('js/modal-cleanup.js') }}"></script>
    <script src="{{ asset('js/panier.js') }}"></script>
    <script src="{{ asset('js/client-auth.js') }}"></script>
    <script src="{{ asset('js/clientRegistration.js') }}"></script>
    @if (app()->environment('local'))
        @vite(['resources/js/app.js'])
    @else
        {{-- En production, charger le bundle compilé si présent --}}
        <script type="module" src="{{ asset('build/assets/app.js') }}"></script>
    @endif

    @stack('scripts')
</body>

</html>
