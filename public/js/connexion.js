// Gestion du formulaire de connexion
document.addEventListener('DOMContentLoaded', function () {
    // Attendre que Bootstrap soit chargé
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap n\'est pas chargé !');
        return;
    }
    const connexionForm = document.getElementById('connexionForm');
    const clientForm = document.getElementById('clientRegistrationForm');
    const clientInfo = document.getElementById('clientInfo');

    if (!connexionForm) return;

    function normalizeTel(raw) {
        if (!raw) return raw;
        let s = String(raw).trim();
        s = s.replace(/\s+/g, '');
        s = s.replace(/[^+\d]/g, '');
        if (s.startsWith('00')) s = '+' + s.slice(2);
        if (s.startsWith('+')) return s;
        const digits = s.replace(/\D/g, '');
        if (digits.length === 9) return '+224' + digits;
        if (digits.length > 9 && digits.startsWith('224')) return '+' + digits;
        return '+' + digits;
    }

    connexionForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const rawTel = document.getElementById('login-tel').value;
        const tel = normalizeTel(rawTel);
        const errorDiv = document.getElementById('login-error');

        // Requête AJAX pour vérifier le client
        fetch('/check-client', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ tel })
        })
            .then(response => response.json().then(data => ({ status: response.status, data })))
            .then(({ status, data }) => {
                console.log('check-client request sent', { tel });
                console.log('check-client response', { status, data });

                // Si le serveur retourne une erreur de validation ou un message d'erreur explicite
                if (data && (data.error || data.message)) {
                    const msg = data.error || data.message;
                    errorDiv.textContent = msg;
                    errorDiv.classList.remove('d-none');
                    return;
                }

                if (data.exists) {
                    // Stocker les infos client avec la même structure que l'inscription
                    const payload = { client: data.clientInfo };
                    localStorage.setItem('clientInfo', JSON.stringify(payload));

                    // Déclencher l'événement global avec la même structure
                    window.dispatchEvent(new CustomEvent('clientInfoChanged', {
                        detail: payload
                    }));

                    // Fermer le modal et nettoyer les backdrops
                    if (typeof bootstrap !== 'undefined') {
                        const modalElement = document.getElementById('connexionModal');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                            // Attendre un peu et nettoyer les restes du modal
                            setTimeout(() => {
                                // Retirer tous les backdrops orphelins
                                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                                // Retirer la classe 'modal-open' du body
                                document.body.classList.remove('modal-open');
                                // Retirer les styles modal du body
                                document.body.style.overflow = 'auto';
                                document.body.style.paddingRight = '0';
                            }, 300); // Attendre la fin de l'animation
                        }
                    }
                } else {
                    // Client non trouvé
                    errorDiv.textContent = "Ce numéro n'est pas enregistré. Veuillez remplir le formulaire d'inscription.";
                    errorDiv.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                errorDiv.textContent = 'Une erreur est survenue, veuillez réessayer.';
                errorDiv.classList.remove('d-none');
            });
    });
});
