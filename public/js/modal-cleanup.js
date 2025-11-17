// Helper pour nettoyer les modales de Bootstrap
function cleanupModalBackdrop() {
    // Retirer tous les backdrops orphelins après 300ms (fin de l'animation)
    setTimeout(() => {
        // Vérifier qu'il n'y a pas d'autres modales ouvertes
        const openModals = document.querySelectorAll('.modal.show').length;
        if (openModals === 0) {
            // Retirer tous les backdrops
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            // Nettoyer les classes du body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
    }, 300);
}

// Écouter les événements de fermeture de modales
document.addEventListener('hidden.bs.modal', (e) => {
    cleanupModalBackdrop();
});
