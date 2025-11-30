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

    <!-- Small UI tweaks for intl-tel-input to show a small flag+code box before the phone input -->
    <style>
        /* Keeps the intl flag/dial-code visually attached to an input, like an input-group prepend */
        .iti {
            display: inline-block;
            vertical-align: middle;
            margin-right: -1px;
            /* align with form-control border */
        }

        .iti__flag-container {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            padding-left: 6px !important;
            padding-right: 6px !important;
        }

        .iti__selected-dial-code {
            margin-left: 6px;
            font-weight: 600;
        }

        /* Remove overlapping left border on the form-control to create unified input effect */
        input[type="tel"].form-control {
            border-left: 0;
        }

        /* Make the small flag box look like an input-group-prepend */
        .iti__flag-container,
        .iti__selected-dial-code {
            border: 1px solid #ced4da;
            border-right: 0;
            background: #fff;
            border-radius: .25rem 0 0 .25rem;
            height: calc(2.25rem + 2px);
        }

        .iti__country-list {
            z-index: 99999;
            /* ensure dropdown is above modals */
        }
    </style>

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
    <!-- JSON-LD Organization schema for SEO -->
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Mourima Market',
            'url' => rtrim(config('app.url'), '/'),
            'logo' => asset('images/logo1.png'),
            'contactPoint' => [[
                '@type' => 'ContactPoint',
                'telephone' => '+224623248567',
                'contactType' => 'Customer service',
            ]],
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
    </script>
</head>

<body>
    <script>
        // Fournit les URLs absolues pour les scripts côté client
        window.APP_URL = "{{ rtrim(config('app.url'), '/') }}";
        window.STORAGE_URL = window.APP_URL + '/storage';
        window.ALL_PRODUIT_URL = "{{ route('produits.allproduit') }}";
    </script>
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
    <!-- Intercept logout forms and redirect to all products page -->
    <script>
        (function() {
            const target = '{{ route('produits.allproduit') }}';
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (!form || !form.action) return;
                if (form.action.indexOf('/logout') === -1 && (form.getAttribute('action') || '').indexOf(
                        '/logout') === -1) return;
                // Only intercept POST logout
                const method = (form.getAttribute('method') || 'GET').toUpperCase();
                if (method !== 'POST') return;
                // Only redirect to products page if the logout is done by a client from the 'Moi' panel.
                const isClientLogout = (typeof window.authUser !== 'undefined' && window.authUser && window
                    .authUser.role === 'client') || (form.closest && (form.closest('#clientInfo') || form
                    .closest('#client-info-content') || form.closest('#side-moi')));
                if (!isClientLogout) {
                    // Let other logout flows behave normally
                    return;
                }
                e.preventDefault();
                // Do AJAX logout and redirect to products page
                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams(new FormData(form)).toString()
                    })
                    .then(response => {
                        try {
                            localStorage.removeItem('clientInfo');
                        } catch (e) {}
                        try {
                            localStorage.removeItem('authUser');
                        } catch (e) {}
                        // Always redirect to allproduit (user requested)
                        window.location.href = target;
                    })
                    .catch(err => {
                        console.warn('Logout failed via AJAX, fallback to form submit', err);
                        // fallback: submit normally
                        form.submit();
                    });
            }, {
                capture: true
            });
        })();
    </script>
</body>

</html>
