@extends('layouts.auth-login')
@section('title', 'Connexion')
@section('content')
    <div class="login-header">
        <div class="login-logo">
            <img src="{{ asset('images/logo1.png') }}" alt="Mourima Market Logo" class="logo-img">
        </div>
        <h1 class="login-title">Bienvenue</h1>
        {{-- <h2 class="login-brand-name">Accès limité qu'aux personnels</h2> --}}
        <p class="login-subtitle">Accès autorisé qu'au personnel</p>
    </div>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                Identifiants invalides. Veuillez réessayer.
            </div>
        @endif

        @include('alerts.alert-message')

        <div class="form-group">
            <label for="email">
                <i class="fas fa-envelope"></i> Email
            </label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="votre@email.com">
            @error('email')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">
                <i class="fas fa-lock"></i> Mot de passe
            </label>
            <div class="password-input-wrapper">
                <input type="password" name="password" id="password"
                    class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password"
                    placeholder="••••••••">
                <i class="fas fa-eye-slash toggle-password" id="toggle-password-icon"></i>
            </div>
            @error('password')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-options">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>
            <a href="{{ route('password.request') }}" class="text-decoration-none">
                <i class="fas fa-question-circle"></i> Mot de passe oublié ?
            </a>
        </div>

        <button class="btn-login" type="submit">
            <i class="fas fa-sign-in-alt"></i> Se connecter
        </button>

        <div class="login-footer">
            <p class="text-muted mb-0">Pas encore de compte ?</p>
            <small class="text-muted">Veuillez consulter l'administrateur pour avoir un accès.</small>
        </div>
    </form>
@endsection
