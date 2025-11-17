@extends('base')

@section('title', 'Définir votre mot de passe')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header text-center text-white" style="background:#1c911e;">
                        <h4>Définir votre mot de passe</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('first.login.set', $user->first_login_token) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nouveau mot de passe</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn text-white" style="background:#1c911e;">
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection