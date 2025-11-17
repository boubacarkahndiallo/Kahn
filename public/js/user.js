// ===============================
// Vérification instantanée champ par champ
// ===============================

// Vérification instantanée du prénom
$('#firstname').on('keyup blur', function () {
    var firstname = $(this).val();
    if (firstname != "" && /^[a-zA-ZÀ-ÿ\s]+$/.test(firstname)) {
        $(this).removeClass('is-invalid').addClass('is-valid');
        $('#error-register-firstname').text('');
    } else {
        $(this).removeClass('is-valid').addClass('is-invalid');
        $('#error-register-firstname').text('First Name is not valid!');
    }
});

// Vérification instantanée du nom
$('#lastname').on('keyup blur', function () {
    var lastname = $(this).val();
    if (lastname != "" && /^[a-zA-ZÀ-ÿ\s]+$/.test(lastname)) {
        $(this).removeClass('is-invalid').addClass('is-valid');
        $('#error-register-lastname').text('');
    } else {
        $(this).removeClass('is-valid').addClass('is-invalid');
        $('#error-register-lastname').text('Last Name is not valid!');
    }
});

// Vérification instantanée de l'email
$('#email').on('keyup blur', function () {
    var email = $(this).val();
    if (email != "" && /^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(email)) {
        $(this).removeClass('is-invalid').addClass('is-valid');
        $('#error-register-email').text('');
    } else {
        $(this).removeClass('is-valid').addClass('is-invalid');
        $('#error-register-email').text('Your email is not valid!');
    }
});

// Vérification instantanée du téléphone
$('#phone').on('keyup blur', function () {
    var phone = $(this).val();
    if (phone != "" && /^[0-9+ ]{8,15}$/.test(phone)) {
        $(this).removeClass('is-invalid').addClass('is-valid');
        $('#error-register-phone').text('');
    } else {
        $(this).removeClass('is-valid').addClass('is-invalid');
        $('#error-register-phone').text('Phone number is not valid!');
    }
});

// Vérification instantanée de l'adresse
$('#address').on('keyup blur', function () {
    var address = $(this).val();
    if (address != "" && address.length >= 5) {
        $(this).removeClass('is-invalid').addClass('is-valid');
        $('#error-register-address').text('');
    } else {
        $(this).removeClass('is-valid').addClass('is-invalid');
        $('#error-register-address').text('Address must be at least 5 characters!');
    }
});

// Vérification instantanée de la date de naissance
$('#dob').on('change', function () {
    var dob = $(this).val();
    if (dob != "") {
        $(this).removeClass('is-invalid').addClass('is-valid');
        $('#error-register-dob').text('');
    } else {
        $(this).removeClass('is-valid').addClass('is-invalid');
        $('#error-register-dob').text('Please provide your date of birth!');
    }
});

// Vérification instantanée du mot de passe
$('#password').on('keyup blur', function () {
    var password = $(this).val();
    if (password.length >= 8) {
        $(this).removeClass('is-invalid').addClass('is-valid');
        $('#error-register-password').text('');
    } else {
        $(this).removeClass('is-valid').addClass('is-invalid');
        $('#error-register-password').text('Password must be at least 8 characters!');
    }
});

// Vérification instantanée de la confirmation du mot de passe
$('#password-confirm').on('keyup blur', function () {
    var password = $('#password').val();
    var password_confirm = $(this).val();
    if (password == password_confirm && password_confirm.length >= 8) {
        $(this).removeClass('is-invalid').addClass('is-valid');
        $('#error-register-password-confirm').text('');
    } else {
        $(this).removeClass('is-valid').addClass('is-invalid');
        $('#error-register-password-confirm').text('Passwords do not match!');
    }
});

// Vérification du genre (radio button)
$('input[name="gender"]').on('change', function () {
    if ($('input[name="gender"]:checked').val() != undefined) {
        $('#error-register-gender').text('');
    } else {
        $('#error-register-gender').text('Please select your gender!');
    }
});

// Vérification des conditions
$('#agreeTerms').change(function () {
    if ($(this).is(':checked')) {
        $('#error-register-agreeterm').text('');
    } else {
        $('#error-register-agreeterm').text("You should agree to our terms and condition!");
    }
});

// ===============================
// Validation finale au clic sur Register
// ===============================
$('#register-user').click(function () {
    var firstname = $('#firstname').val();
    var lastname = $('#lastname').val();
    var email = $('#email').val();
    var phone = $('#phone').val();
    var address = $('#address').val();
    var dob = $('#dob').val();
    var password = $('#password').val();
    var password_confirm = $('#password-confirm').val();
    var agreeTerms = $('#agreeTerms');

    // Vérifie si l'email existe déjà
    var res = emailExistjs(email);

    if (
        firstname != "" && /^[a-zA-ZÀ-ÿ\s]+$/.test(firstname) &&
        lastname != "" && /^[a-zA-ZÀ-ÿ\s]+$/.test(lastname) &&
        email != "" && /^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(email) &&
        phone != "" && /^[0-9+ ]{8,15}$/.test(phone) &&
        address != "" && address.length >= 5 &&
        dob != "" &&
        password.length >= 8 &&
        password == password_confirm &&
        agreeTerms.is(':checked') &&
        res != "exist"
    ) {
        $('#form-register').submit();
    } else {
        if (res == "exist") {
            $('#email').removeClass('is-valid').addClass('is-invalid');
            $('#error-register-email').text("This email address is already used!");
        }
    }
});

// ===============================
// Fonction AJAX pour vérifier si l'email existe
// ===============================
function emailExistjs(email) {
    var url = $('#email').attr('url-emailExist');
    var token = $('#email').attr('token');
    var res = "";
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            '_token': token,
            email: email
        },
        success: function (result) {
            res = result.response;
        },
        async: false
    });
    return res;
}
