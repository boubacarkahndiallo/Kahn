<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Mourima Market</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">

    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-1BmE4kWBq78iYhFldwKuhfstJSn7MKPbjLT8+IVfZpMvscyjqCxnLn4+71CspJ7wQ" crossorigin="anonymous">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    {{-- Custom Auth Login CSS --}}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, rgba(28, 145, 30, 0.85) 0%, rgba(255, 255, 255, 0.85) 100%),
                url('{{ asset('images/banner-06.jpg') }}') center/cover no-repeat;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #1c911e;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #1c911e;
            font-size: 20px;
            z-index: 1000;
        }

        .back-button:hover {
            background: #1c911e;
            color: white;
            transform: translateX(-3px);
        }

        .back-button:active {
            transform: translateX(-1px);
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 40px;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-logo {
            /* margin-bottom: 12px; */
            display: flex;
            justify-content: center;
        }

        .logo-img {
            max-width: 80px;
            height: auto;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .login-logo:hover .logo-img {
            transform: scale(1.05);
        }

        .login-brand-name {
            color: #1c911e;
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .login-title {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #e20505;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
            width: 100%;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #1c911e;
            background-color: white;
            box-shadow: 0 0 0 0.2rem rgba(28, 145, 30, 0.1);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.1);
        }

        /* Password input wrapper with eye toggle */
        .password-input-wrapper {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .password-input-wrapper .form-control {
            padding-right: 45px;
            width: 100%;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            font-size: 18px;
            transition: all 0.3s ease;
            user-select: none;
            pointer-events: all;
            z-index: 10;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: #1c911e;
            transform: translateY(-50%) scale(1.1);
        }

        .toggle-password:active {
            transform: translateY(-50%) scale(0.95);
        }

        .form-check {
            margin-bottom: 15px;
        }

        .form-check-input {
            border: 2px solid #e0e0e0;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: #1c911e;
            border-color: #1c911e;
        }

        .form-check-input:focus {
            border-color: #1c911e;
            box-shadow: 0 0 0 0.2rem rgba(28, 145, 30, 0.25);
        }

        .form-check-label {
            color: #555;
            font-size: 14px;
            margin-left: 8px;
            cursor: pointer;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .form-options a {
            color: #1c911e;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .form-options a:hover {
            color: #145a14;
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 12px 20px;
            background-color: #1c911e;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-login:hover {
            background-color: #145a14;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(28, 145, 30, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            color: #666;
            font-size: 13px;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
            margin-top: 10px;
        }

        .login-footer p {
            margin-bottom: 5px;
            color: #666;
            font-weight: 500;
        }

        .login-footer small {
            display: block;
            color: #999;
            font-size: 12px;
            line-height: 1.4;
        }

        .login-footer a {
            color: #1c911e;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-footer a:hover {
            color: #145a14;
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background-color: #f0f9f1;
            color: #1c911e;
            border-left: 4px solid #1c911e;
        }

        .alert-info {
            background-color: #f0f5ff;
            color: #004085;
            border-left: 4px solid #004085;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 30px 20px;
            }

            .login-title {
                font-size: 24px;
            }

            .login-logo {
                font-size: 28px;
            }

            .form-options {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <a href="{{ route('app_accueil') }}" class="back-button" title="Retour">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="login-container">
        @yield('content')
    </div>

    {{-- Bootstrap Bundle JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP28lgoX57ewc5gD0jYwoCn8YzIUKjc" crossorigin="anonymous">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const rememberCheckbox = document.getElementById('remember');
            const loginForm = document.querySelector('form');

            // Restaurer l'email sauvegardé et cocher "Se souvenir de moi"
            if (localStorage.getItem('rememberedEmail')) {
                emailInput.value = localStorage.getItem('rememberedEmail');
                rememberCheckbox.checked = true;
            }

            // Sauvegarder l'email si "Se souvenir de moi" est coché
            loginForm.addEventListener('submit', function(e) {
                if (rememberCheckbox.checked && emailInput.value) {
                    localStorage.setItem('rememberedEmail', emailInput.value);
                } else {
                    localStorage.removeItem('rememberedEmail');
                }
            });

            // Effacer le localStorage quand on décoche
            rememberCheckbox.addEventListener('change', function() {
                if (!this.checked) {
                    localStorage.removeItem('rememberedEmail');
                    emailInput.value = '';
                }
            });

            // Toggle password visibility
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-password-icon');

            if (toggleIcon && passwordInput) {
                toggleIcon.addEventListener('click', function() {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    } else {
                        passwordInput.type = 'password';
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
