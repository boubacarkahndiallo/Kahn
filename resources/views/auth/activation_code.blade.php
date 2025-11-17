@extends('base')
@section('title', 'Activation Account')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 mx-auto">
                <h1 class="text-center text-muted mb-3 mt-5">Account Activation </h1>
                @include('alerts.alert-message')

                <form method="POST" action="{{ route('app_activation_code', ['token' => $token]) }}">
                    @csrf
                    <label for="activation-code" class="form-label">Activation code</label>
                    <input type="text" name="activation-code"
                        class="form-control @if (Session::has('danger')) is-invalid @endif" id="activation-code"
                        required autocomplete="activation-code" autofocus
                        value="@if (Session::has('activation_code')) {{ Session::get('activation_code') }} @endif">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <a href="{{ route('app_activation_account_change_email', ['token' => $token]) }}">Change your
                                email address</a>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('app_resend_activation_code', ['token' => $token]) }}">Resend the activation
                                code</a>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary mt-2"> Activate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
