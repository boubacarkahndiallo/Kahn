<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Validator::make($input, [
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => [
        //         'required',
        //         'string',
        //         'email',
        //         'max:255',
        //         Rule::unique(User::class),
        //     ],
        //     'password' => $this->passwordRules(),
        // ])->validate();

        $email = $input['email'];
        // Pour génèré le token pour l'activation du compt de l'utilisateur
        $activation_token = md5(uniqid()) . $email . sha1($email);
        $activation_code = "";
        $length_code = 5;
        for ($i = 0; $i < $length_code; $i++) {
            // Pour la généré des chiffre aleatoir de 0 à 9
            $activation_code .= mt_rand(0, 9);
        }

        $name = $input['firstname'] . ' ' . $input['lastname'];

        // Pour envoyé des email
        $emailSend = new EmailService;
        $subject = "Activate your Account";
        // pour passé le message
        // $message = view('mail.confirmation_email')
        //     ->with([
        //         'name' => $name,
        //         'activation_code' => $activation_code,
        //         'activation_token' => $activation_token,

        //     ]);
        // $message = "Hi " . $name . "please activate your account.Copy and past your activation code: " . $activation_code .
        //     ". Or click to the link bellow to activate your account, link:" . $activation_token;
        // pour envoyé
        $emailSend->sendEmail($subject, $email, $name, true, $activation_code, $activation_token);
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($input['password']),
            'activation_code' => $activation_code,
            'activation_token' => $activation_token,

        ]);
    }
}
