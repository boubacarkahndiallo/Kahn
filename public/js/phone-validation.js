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

    // Initialiser les champs de téléphone
    const telInput = document.getElementById('tel');
    const whatsappInput = document.getElementById('whatsapp');
    let telInstance, whatsappInstance;

    if (telInput) {
        telInstance = window.intlTelInput(telInput, telInputOptions);
        setupPhoneValidation(telInput, telInstance);
    }

    if (whatsappInput) {
        whatsappInstance = window.intlTelInput(whatsappInput, telInputOptions);
        setupPhoneValidation(whatsappInput, whatsappInstance);
    }

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
        const feedback = input.nextElementSibling;

        if (input.value.trim()) {
            if (instance.isValidNumber()) {
                input.classList.add('is-valid');
                return true;
            } else {
                const errorCode = instance.getValidationError();
                const errorMsg = errorMap[errorCode] || "Numéro invalide";
                input.classList.add('is-invalid');
                if (feedback) feedback.textContent = errorMsg;
                return false;
            }
        }
        return false;
    }

    // Intercepter la soumission du formulaire
    const inscriptionForm = document.getElementById('inscriptionForm');
    if (inscriptionForm) {
        inscriptionForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Valider le numéro de téléphone principal
            if (!validatePhone(telInput, telInstance)) {
                return;
            }

            // Valider le numéro WhatsApp s'il est rempli
            if (whatsappInput.value.trim() && !validatePhone(whatsappInput, whatsappInstance)) {
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
                        tel: telInstance.getNumber()
                    })
                });

                const data = await response.json();

                if (data.exists) {
                    telInput.classList.add('is-invalid');
                    telInput.nextElementSibling.textContent = "Ce numéro est déjà enregistré";
                    return;
                }

                // Si le numéro n'existe pas, mettre à jour les valeurs avec le format international
                telInput.value = telInstance.getNumber();
                if (whatsappInput.value.trim()) {
                    whatsappInput.value = whatsappInstance.getNumber();
                }

                // Soumettre le formulaire
                this.submit();

            } catch (error) {
                console.error('Erreur lors de la vérification du numéro:', error);
            }
        });
    }
});
