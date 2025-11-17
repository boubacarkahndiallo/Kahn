// Fonction pour vérifier si l'utilisateur est connecté
function isUserLoggedIn() {
    const clientInfo = localStorage.getItem('clientInfo');
    return clientInfo !== null || (typeof window.authUser !== 'undefined' && window.authUser !== null);
}

// Fonction pour désactiver les sélections de produits
function disableProductSelections(tableElement) {
    if (!tableElement) return;

    // Désactiver toutes les cases à cocher
    tableElement.querySelectorAll('.select-produit').forEach(checkbox => {
        checkbox.disabled = true;
        checkbox.checked = false;
        const tr = checkbox.closest('tr');
        const qtyInput = tr.querySelector('.qty');
        if (qtyInput) {
            qtyInput.disabled = true;
            qtyInput.value = 1;
        }
        const btnAnnuler = tr.querySelector('.btn-annuler');
        if (btnAnnuler) {
            btnAnnuler.classList.add('d-none');
        }
    });

    // Désactiver le "Tout sélectionner"
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.disabled = true;
        selectAll.checked = false;
    }

    // Mettre à jour les totaux
    const grandTotal = document.getElementById('grand-total');
    if (grandTotal) {
        grandTotal.textContent = '0';
    }

    // Cacher le bouton commander
    const btnCommander = document.getElementById('btn-commander');
    if (btnCommander) {
        btnCommander.classList.add('d-none');
    }
}

// Fonction pour afficher le message d'avertissement
function showAuthWarning(tableElement) {
    if (!tableElement) return;

    const alert = document.createElement('div');
    alert.className = 'alert alert-warning mb-4';
    alert.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Attention !</strong> Pour sélectionner des produits, veuillez
        <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#connexionModal">vous connecter</a>
        si vous avez déjà un compte, ou remplir le formulaire ci-dessous.
    `;
    // Insérer l'alerte juste en haut du formulaire d'inscription
    const clientForm = document.getElementById('clientRegistrationForm');
    if (clientForm) {
        clientForm.parentNode.insertBefore(alert, clientForm);
    } else {
        // Fallback : insérer avant le tableau si le formulaire n'existe pas
        tableElement.parentNode.insertBefore(alert, tableElement);
    }
}

// Fonction principale pour gérer l'état d'authentification
function handleAuthState() {
    const tables = document.querySelectorAll('#produits-table');
    if (!isUserLoggedIn()) {
        tables.forEach(table => {
            disableProductSelections(table);
            showAuthWarning(table);
        });
    }
}

// Exécuter au chargement du DOM
document.addEventListener('DOMContentLoaded', handleAuthState);
