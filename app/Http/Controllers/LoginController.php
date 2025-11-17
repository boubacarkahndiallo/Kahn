<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\ViewName;
use App\Services\EmailService;


class LoginController extends Controller
{
    // pour crée une requette qui sera utilisé globalement
    protected $request;
    function __construct(Request $request)
    {
        $this->request = $request;
    }

    /*
    public function login()
    {
        return view('auth.login');
    }
    public function register()
    {
        return view('auth.register');
    }
*/
    // Function pour se deconecté
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    // function pour envoyé du code par e-mail
    public function existEmail()
    {
        // La valeur à recupéré après l'envoi d'une requette
        $email = $this->request->input('email');
        // -----
        $user = User::where('email', $email)->first();
        $response = "";
        // La condition ternaire si l'utilisateur exit response va resevoir exit oubien n'existe pas
        // C'est le raccourci de
        // if ($user) {
        //     $response = "exist";
        // } else {
        //     $response = "not_exist";
        // }

        ($user) ? $response = "exist" : $response = "not_exist";
        // ------
        return response()->json([
            // 'code' => 200,
            'response' => $response
        ]);
    }

    // function pour activation du compt avec un code envoyé par email
    public function activationCode($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('danger', 'This token doesn\'t match any user.');
        }

        if ($this->request->isMethod('post')) {
            $code = $user->activation_code;

            $activation_code = $this->request->input('activation-code');

            if ($activation_code != $code) {
                return back()->with([
                    'danger' => 'This activation code is invalid !!',
                    'activation_code' => $activation_code
                ]);
            } else {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'is_verified' => 1,
                        'activation_code' => '',
                        'activation_token' => '',
                        'email_verified_at' => new \DateTimeImmutable,
                        'updated_at' => new \DateTimeImmutable,
                    ]);
                return redirect()->route('login')->with('success', 'Your e-mail address has been verified !');
            }
        }
        return view('auth.activation_code', [
            'token' => $token
        ]);
    }


    // verifié si l'utlisateur a déjà activé sont compt ou pas

    public function userChecker()
    {
        $user = Auth::user();
        $activation_token = $user->activation_token;
        $is_verified = $user->is_verified;

        // Si l'utilisateur n'a pas de token d'activation (compte déjà activé ou créé sans processus d'activation)
        if (empty($activation_token)) {
            return redirect()->route('app_dashboard');
        }

        if ($is_verified != 1) {
            return redirect()->route('app_activation_code', ['token' => $activation_token])
                ->with('warning', 'Your account is not activate yet, please check your
                    mail-box and activate your account or resend the confirmation message.');
        } else {
            return redirect()->route('app_dashboard');
        }
    }


    // function pour renvoyé le code d'activation
    public function resendActivationCode($token)
    {
        $user = User::where('activation_token', $token)->first();
        $email = $user->email;
        $name = $user->name;
        $activation_token = $user->activation_token;
        $activation_code = $user->activation_code;
        // Pour envoyé des email
        $emailSend = new EmailService;
        $subject = "Activate your Account";
        // pour passé le message
        // pour envoyé
        $emailSend->sendEmail($subject, $email, $name, true, $activation_code, $activation_token);

        return back()->with('success', 'You have just resend the new activation code.');
    }

    // Function pour activité à travers le lien envoyé par e-mail
    public function activationAccountLink($token)
    {
        $user = User::where('activation_token', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('danger', 'This token doesn\'t match any user.');
        }
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'is_verified' => 1,
                'activation_code' => '',
                'activation_token' => '',
                'email_verified_at' => new \DateTimeImmutable,
                'updated_at' => new \DateTimeImmutable,
            ]);
        return redirect()->route('login')->with('success', 'Your e-mail address has been verified !');
    }

    // pour changer l'adress email
    public function activationAccountChangeEmail($token)
    {
        $user = User::where('activation_token', $token)->first();
        if ($this->request->isMethod('post')) {
            $new_email = $this->request->input('new-email');
            $user_existe = User::where('email', $new_email)->first();
            if ($user_existe) {
                return back()->with([
                    'danger' => 'This address email is already used! please change enter another email address !',
                    'new_email' => $new_email
                ]);
            } else {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'email' => $new_email,
                        'updated_at' => new \DateTimeImmutable
                    ]);

                $activation_code = $user->activation_code;
                $activation_token = $user->activation_token;
                $name = $user->name;

                $emailSend = new EmailService;
                $subject = "Activate your Account";
                // pour passé le message
                // $message = view('mail.confirmation_email')
                //     ->with([
                //         'name' => $name,
                //         'activation_code' => $activation_code,
                //         'activation_token' => $activation_token,

                //     ]);
                // pour envoyé
                // $emailSend->sendEmail($subject, $new_email, $name, true, $message);
                $emailSend->sendEmail($subject, $new_email, $name, true, $activation_code, $activation_token);

                return redirect()
                    ->route('app_activation_code', ['token' => $token])
                    ->with('success', 'You have just resend the new activation code.');
            }
        } else {
            return view('auth.activation_account_change_email', ['token' => $token]);
        }
    }
}
