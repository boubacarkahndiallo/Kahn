// Script pour la vérification de l'enregistrement des utilisateurs
$('#register-user').click(function () {
    var firstname = $('#firstname').val();
    var lastname = $('#lastname').val();
    var email = $('#email').val();
    var password = $('#password').val();
    var password_confirm = $('#password-confirm').val();
    var passwordLength = password.length;
    var agreeTerms = $('#agreeTerms');
    if (firstname != "" && /^[a-zA-Z ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöĒĔĖĚēėéêëç ÇȊȋÙÚÛïîÏÜùúûüÿŇń]+$/.test(firstname)) {
        // console.log("working");
        $('#firstname').removeClass('is-invalid');
        $('#firstname').addClass('is-valid');
        $('#error-register-firstname').text('');
        // alert('working');
        if (lastname != "" && /^[a-zA-Z ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöĒĔĖĚēėéêëçÇȊȋÙÚÛÜïîÏùúûüÿŇń]+$/.test(lastname)) {
            $('#lastname').removeClass('is-invalid');
            $('#lastname').addClass('is-valid');
            $('#error-register-lastname').text("");
            if (email != "" && /^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(email)) {
                $('#email').removeClass('is-invalid');
                $('#email').addClass('is-valid');
                $('#error-register-email').text("");
                if (passwordLength >= 8) {
                    $('#password').removeClass('is-invalid');
                    $('#password').addClass('is-valid');
                    $('#error-register-password').text("");

                    if (password == password_confirm) {
                        $('#password-confirm').removeClass('is-invalid');
                        $('#password-confirm').addClass('is-valid');
                        $('#error-register-password-confirm').text("");
                        if (agreeTerms.is(':checked')) {
                            $('#agreeTerms').removeClass('is-valid');
                            $('#error-register-agreeterm').text("");

                            // envoi du formulaire
                            var res = emailExistjs(email);

                            (res != "exist") ? $('#form-register').submit()
                                : ($('#email').addClass('is-invalid'),
                                    $('#email').removeClass('is-valid'),
                                    $('#error-register-email').text("This email addresse is already used!"));

                            /**
                             * condition ternaire
                             * (condition)?vraie: fausse;
                             * condition avec plusieur instructions
                             * (condition) ? vraie (inst1, inst2) : fause((inst1, inst2));
                             *                              */
                            // $('#form-register').submit();
                        } else {
                            $('#agreeTerms').addClass('is-valid');
                            $('#error-register-agreeterm').text("You should agree to our terms and condition!");
                        }


                    } else {
                        $('#password-confirm').addClass('is-invalid');
                        $('#password-confirm').removeClass('is-valid');
                        $('#error-register-password-confirm').text("Your password must be identical!");

                    }
                } else {
                    $('#password').addClass('is-invalid');
                    $('#password').removeClass('is-valid');
                    $('#error-register-password').text('Your password must be at last 8 characteres!');
                }
            } else {
                $('#email').addClass('is-invalid');
                $('#email').removeClass('is-valid');
                $('#error-register-email').text('Your email is not valid!');
            }
        } else {
            $('#lastname').addClass('is-invalid');
            $('#lastname').removeClass('is-valid')
            $('#error-register-lastname').text("Last Name is not valid!")
        }
    } else {
        $('#firstname').addClass('is-invalid');
        $('#firstname').removeClass('is-valid');
        $('#error-register-firstname').text('First Name is not valid!');
    }
});

// Evenement pour le termes et condition
$('#agreeTerms').change(function () {
    var agreeTerms = $('#agreeTerms');
    if (agreeTerms.is(':checked')) {
        $('#agreeTerms').removeClass('is-valid');
        $('#error-register-agreeterm').text("");

    } else {
        $('#agreeTerms').addClass('is-valid');
        $('#error-register-agreeterm').text("You should agree to our terms and condition!");
    }
});

// Function si l'email existe déjà ou non
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
