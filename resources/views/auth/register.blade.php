@extends('base')
@section('title', 'Register')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-5 mx-auto">
                <h1 class="text-center text-muted md-3 mt-5">Register</h1>
                <p class="text-center text-muted mb-5">Create an account if you don't have one </p>

                <form action="{{ route('register') }}" method="post" class="row g-3" id="form-register">
                    @csrf
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">Firs Name</label>
                        <input type="text" name="firstname" id="firstname" class="form-control "
                            value="{{ old('firstname') }}" required autocomplete="firstname" autofocus>
                        <small class="text-danger fw-bold" id="error-register-firstname"></small>
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" name="lastname" id="lastname" class="form-control"
                            value="{{ old('lastname') }}" required autocomplete="lastname" autofocus>
                        <small class="text-danger fw-bold" id="error-register-lastname"></small>
                    </div>
                    <div class="col-md-12">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                            required autocomplete="email" url-emailExist="{{ route('app_exist_email') }}"
                            token="{{ csrf_token() }}">
                        <div class="small text-danger fw-bold" id="error-register-email"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control"
                            value="{{ old('password') }}" required autocomplete="password" autofocus>
                        <small class="text-danger fw-bold" id="error-register-password"></small>
                    </div>
                    <div class="col-md-6">
                        <label for="password-confirm" class="form-label">Password Confirmation</label>
                        <input type="password" name="password-confirm" id="password-confirm" class="form-control"
                            value="{{ old('password-confirm') }}" required autocomplete="password-confirm" autofocus>
                        <small class="text-danger fw-bold" id="error-register-password-confirm"></small>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" value="" id="agreeTerms"
                                name="agreeTerms">
                            <label for="agreeTerms" class="form-check-label">Agree terms</label><br>
                            <small class="text-danger fw-bold" id="error-register-agreeterm"></small>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="button" id="register-user">Register</button>
                    </div>
                    <p class="text-center text-muted mt-5">Already have an account ? <a
                            href="{{ route('login') }}">login</a></p>
                </form>
            </div>
        </div>
    </div>
@endsection
