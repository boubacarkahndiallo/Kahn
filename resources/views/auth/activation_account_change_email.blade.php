@extends('base')

@section('title', 'Change your email address')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 mx-auto">
                <h3 class="text-center text-muted mb-3 mt-5">Change your email address</h3>
                @include('alerts.alert-message')
                <form action="{{ route('app_activation_account_change_email', ['token' => $token]) }}" method="POST">
                    @csrf
                    <label for="new-email" class="form-label text-muted">New Email address</label>
                    <input type="email" class="form-control @if (Session::has('danger')) is-invalid @endif"
                        name="new-email" id="new-email"
                        value="@if (Session::has('new_email')) {{ Session::get('new_email') }} @endif"
                        placeholder="Enter the new emai address " required>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary mt-2"> Change</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
