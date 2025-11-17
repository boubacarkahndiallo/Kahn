// Script pour la vérification de l'enregistrement des locataires
$('#register-locataire').click(function () {
    var nom = $('#nom').val();
    var prenom = $('#prenom').val();
    var telephone = $('#telephone').val();
    var email = $('#email').val();
    var adresse = $('#adresse').val();
    var date_naissance = $('#date_naissance').val();
    var profession = $('#profession').val();
    var photo = $('#photo').val();
    var piece_identite = $('#piece_identite').val();

    // Regex
    var nameRegex = /^[a-zA-Z ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöĒĔĖĚēėéêëçÇȊȋÙÚÛÜïîÏùúûüÿŇń]+$/;
    var emailRegex = /^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/;
    var phoneRegex = /^[0-9+ ]{8,20}$/;
    var isValid = true;

    // Nom
    if (nom !== "" && nameRegex.test(nom)) {
        $('#nom').removeClass('is-invalid').addClass('is-valid');
        $('#error-nom').text(''); 
    } else {
        $('#nom').addClass('is-invalid').removeClass('is-valid');
        $('#error-nom').text('Nom invalide!');
        isValid = false;
    }

    // Prénom
    if (prenom !== "" && nameRegex.test(prenom)) {
        $('#prenom').removeClass('is-invalid').addClass('is-valid');
        $('#error-prenom').text('');
    } else {
        $('#prenom').addClass('is-invalid').removeClass('is-valid');
        $('#error-prenom').text('Prénom invalide!');
        isValid = false;
    }

    // Téléphone
    if (telephone !== "" && phoneRegex.test(telephone)) {
        $('#telephone').removeClass('is-invalid').addClass('is-valid');
        $('#error-telephone').text('');
    } else {
        $('#telephone').addClass('is-invalid').removeClass('is-valid');
        $('#error-telephone').text('Numéro de téléphone invalide!');
        isValid = false;
    }

    // Email
    if (email !== "" && emailRegex.test(email)) {
        $('#email').removeClass('is-invalid').addClass('is-valid');
        $('#error-email').text('');
    } else {
        $('#email').addClass('is-invalid').removeClass('is-valid');
        $('#error-email').text('Email invalide!');
        isValid = false;
    }

    // Date de naissance
    if (date_naissance !== "") {
        $('#date_naissance').removeClass('is-invalid').addClass('is-valid');
        $('#error-date_naissance').text('');
    } else {
        $('#date_naissance').addClass('is-invalid').removeClass('is-valid');
        $('#error-date_naissance').text('Veuillez sélectionner la date de naissance!');
        isValid = false;
    }

    // Profession
    if (profession !== "") {
        $('#profession').removeClass('is-invalid').addClass('is-valid');
        $('#error-profession').text('');
    } else {
        $('#profession').addClass('is-invalid').removeClass('is-valid');
        $('#error-profession').text('Veuillez saisir la profession!');
        isValid = false;
    }

    // Pièce d'identité
    if (piece_identite !== "") {
        $('#piece_identite').removeClass('is-invalid').addClass('is-valid');
        $('#error-piece_identite').text('');
    } else {
        $('#piece_identite').addClass('is-invalid').removeClass('is-valid');
        $('#error-piece_identite').text('Veuillez fournir une pièce d\'identité!');
        isValid = false;
    }

    // Prévisualisation photo déjà gérée par HTML + JS existant

    // Si tout est valide, soumettre le formulaire
    if (isValid) {
        $('#formLocataire').submit();
    }
});

// Prévisualisation de l'image
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('previewPhoto');
    const iconPlus = document.getElementById('iconPlus');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            iconPlus.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
