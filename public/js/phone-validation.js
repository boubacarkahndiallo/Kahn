// Initialisation des champs de téléphone avec intl-tel-input
document.addEventListener('DOMContentLoaded', function () {
    // Options communes pour intl-tel-input
    const telInputOptions = {
        initialCountry: "gn", // Guinea
        preferredCountries: ["gn", "sn", "ci", "ml"], // Guinea, Senegal, Côte d'Ivoire, Mali
        separateDialCode: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
        customPlaceholder: function (selectedCountryPlaceholder, selectedCountryData) {
            return "Ex: " + selectedCountryPlaceholder;
        }
    };

    // Exposer map d'instances pour d'autres scripts (ex: pré-remplissage)
    window.intlTelInputInstances = window.intlTelInputInstances || {};
    window.intlTelInputInstanceMap = window.intlTelInputInstanceMap || new Map();

    // Injecter quelques styles pour intégrer le drapeau dans input-group proprement
    const style = document.createElement('style');
    style.textContent = `
        .input-group .iti { position: static !important; display:inline-flex; align-items:center; }
        .input-group .iti * { box-sizing: border-box; }
        .input-group .phone-flag { padding: .375rem .5rem; }
        .input-group .iti .iti__selected-flag { margin-right: 0; }
    `;
    document.head.appendChild(style);

    // Initialiser tous les inputs type=tel sur la page
    const telElements = document.querySelectorAll('input[type="tel"]');
    telElements.forEach((el, idx) => {
        try {
            if (el.dataset.itiInitialized) return;
            const instance = window.intlTelInput(el, telInputOptions);
            el.dataset.itiInitialized = '1';
            // Stocker une référence utilisable (id ou name sinon index)
            const key = el.id || el.name || ('tel-' + idx);
            window.intlTelInputInstances[key] = instance;
            window.intlTelInputInstanceMap.set(el, instance);
            ensureInputGroupFlag(el);
            moveItiFlagToInput(el);
            setupPhoneValidation(el, instance);
        } catch (err) {
            console.warn('Erreur initialisation intlTelInput pour', el, err);
        }
    });

    // Configuration de la validation
    function setupPhoneValidation(input, instance) {
        input.addEventListener('blur', function () {
            validatePhone(input, instance);
        });

        input.addEventListener('change', function () {
            validatePhone(input, instance);
        });

        input.addEventListener('keyup', function () {
            validatePhone(input, instance);
        });
    }

    // Créer le container .input-group-text.phone-flag si absent
    function ensureInputGroupFlag(input) {
        try {
            const group = input.closest('.input-group');
            if (!group) return;
            let flagSlot = group.querySelector('.phone-flag');
            if (!flagSlot) {
                flagSlot = document.createElement('span');
                flagSlot.className = 'input-group-text phone-flag';
                group.insertBefore(flagSlot, group.firstElementChild);
            }
        } catch (e) {
            console.warn('Erreur ensureInputGroupFlag:', e);
        }
    }

    // Move the generated .iti element produced by intlTelInput into .phone-flag
    function moveItiFlagToInput(input) {
        try {
            // The plugin creates a .iti element next to the input; we wait a tick to ensure it exists
            setTimeout(function () {
                const group = input.closest('.input-group');
                if (!group) return;
                const iti = group.querySelector('.iti');
                // If not found inside group, pick sibling
                let itiElement = iti;
                if (!itiElement) {
                    itiElement = input.parentNode.querySelector('.iti') || input.nextElementSibling && input.nextElementSibling.classList && input.nextElementSibling.classList.contains('iti') ? input.nextElementSibling : null;
                }
                const flagSlot = group.querySelector('.phone-flag');
                if (itiElement && flagSlot && flagSlot !== itiElement.parentNode) {
                    // Move outer container of the flag (so CSS from plugin remains)
                    flagSlot.appendChild(itiElement);
                    // Ensure the inserted root has static position for layout
                    itiElement.style.position = 'static';
                    itiElement.classList.add('iti-in-input-group');
                }
            }, 0);
        } catch (err) {
            console.warn('moveItiFlagToInput err', err);
        }
    }

    // Fonction de validation
    function validatePhone(input, instance) {
        const errorMap = [
            "Numéro invalide",
            "Code pays invalide",
            "Numéro trop court",
            "Numéro trop long",
            "Numéro invalide"
        ];

        // Réinitialiser les classes et messages d'erreur
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
        // Prefer the .invalid-feedback inside the group, then the group's next sibling, then the input's next sibling
        let feedback = null;
        const group = input.closest('.input-group');
        if (group) {
            feedback = group.querySelector('.invalid-feedback') || group.nextElementSibling;
        }
        if (!feedback) feedback = input.nextElementSibling;

        if (input.value.trim()) {
            if (instance.isValidNumber()) {
                input.classList.add('is-valid');
                if (feedback) { feedback.textContent = ''; feedback.style.display = 'none'; }
                return true;
            } else {
                const errorCode = instance.getValidationError();
                const errorMsg = errorMap[errorCode] || "Numéro invalide";
                input.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = errorMsg;
                    feedback.style.display = '';
                }
                return false;
            }
        }
        return false;
    }

    // Intercepter la soumission du formulaire
    const inscriptionForm = document.getElementById('inscriptionForm');
    const clientRegistrationForm = document.getElementById('clientRegistrationForm');
    if (inscriptionForm) {
        inscriptionForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formTelInput = inscriptionForm.querySelector('input[name="tel"]');
            const formWhatsappInput = inscriptionForm.querySelector('input[name="whatsapp"]');
            const formTelInstance = window.intlTelInputInstanceMap.get(formTelInput);
            const formWhatsappInstance = window.intlTelInputInstanceMap.get(formWhatsappInput);

            // Valider le numéro de téléphone principal
            if (formTelInput && formTelInstance && !validatePhone(formTelInput, formTelInstance)) {
                return;
            }

            // Valider le numéro WhatsApp s'il est rempli
            if (formWhatsappInput && formWhatsappInstance && formWhatsappInput.value.trim() && !validatePhone(formWhatsappInput, formWhatsappInstance)) {
                return;
            }

            // Vérifier si le numéro existe déjà
            try {
                const response = await fetch('/check-client', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                            tel: formTelInstance ? formTelInstance.getNumber() : (formTelInput ? formTelInput.value : '')
                        })
                });

                const data = await response.json();

                if (data.exists) {
                    if (formTelInput) {
                        formTelInput.classList.add('is-invalid');
                        const fb = formTelInput.closest('.input-group') ? formTelInput.closest('.input-group').querySelector('.invalid-feedback') : formTelInput.nextElementSibling;
                        if (fb) fb.textContent = "Ce numéro est déjà enregistré";
                    }
                    return;
                }

                // Si le numéro n'existe pas, mettre à jour les valeurs avec le format international
                if (formTelInput && formTelInstance) formTelInput.value = formTelInstance.getNumber();
                if (formWhatsappInput && formWhatsappInstance && formWhatsappInput.value.trim()) {
                    formWhatsappInput.value = formWhatsappInstance.getNumber();
                }

                // Soumettre le formulaire
                this.submit();

            } catch (error) {
                console.error('Erreur lors de la vérification du numéro:', error);
            }
        });
    }
    // Supporter aussi le formulaire clientRegistration (utilisé sur la page de produits)
    if (clientRegistrationForm) {
        clientRegistrationForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formTelInput = clientRegistrationForm.querySelector('input[name="tel"]');
            const formWhatsappInput = clientRegistrationForm.querySelector('input[name="whatsapp"]');
            const formTelInstance = window.intlTelInputInstanceMap.get(formTelInput);
            const formWhatsappInstance = window.intlTelInputInstanceMap.get(formWhatsappInput);
            // Valider le numéro principal
            if (formTelInput && formTelInstance && !validatePhone(formTelInput, formTelInstance)) {
                showFeedback(formTelInput, 'Numéro invalide');
                return;
            }
            // Valider Whatsapp si rempli
            if (formWhatsappInput && formWhatsappInstance && formWhatsappInput.value.trim() && !validatePhone(formWhatsappInput, formWhatsappInstance)) {
                showFeedback(formWhatsappInput, 'Numéro invalide');
                return;
            }
            // Replace inputs with E.164 international form
            if (formTelInput && formTelInstance) {
                formTelInput.value = formTelInstance.getNumber();
            }
            if (formWhatsappInput && formWhatsappInstance && formWhatsappInput.value.trim()) {
                formWhatsappInput.value = formWhatsappInstance.getNumber();
            }
            // Submit the form
            clientRegistrationForm.submit();
        });
    }

    function showFeedback(input, message) {
        try {
            const group = input.closest('.input-group');
            const feedback = group ? group.querySelector('.invalid-feedback') : input.nextElementSibling;
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            if (feedback) {
                feedback.textContent = message;
                feedback.style.display = '';
            }
        } catch (e) { /* ignore */ }
    }
});
