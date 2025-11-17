<!-- Modal Connexion -->
<div class="modal fade" id="connexionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background:#1c911e;">
                <h5 class="modal-title text-white"><i class="fa fa-sign-in-alt me-2"></i> Connexion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('login') }}" id="connexionForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="login-tel">Votre numéro de téléphone</label>
                        <input type="text" id="login-tel" class="form-control" name="tel" required
                            placeholder="Ex: 620123456">
                    </div>
                    <div class="alert alert-danger d-none" id="login-error"></div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn" style="background:#070a23; color:white;"
                        data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn" style="background:#1c911e; color:white;">
                        <i class="fa fa-sign-in-alt me-1"></i> Se connecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
