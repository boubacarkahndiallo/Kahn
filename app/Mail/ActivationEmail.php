<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $activation_code;
    public $activation_token;

    /**
     * Crée une nouvelle instance de message.
     */
    public function __construct($user, $activation_code, $activation_token)
    {
        $this->user = $user;
        $this->activation_code = $activation_code;
        $this->activation_token = $activation_token;
    }

    /**
     * Construction du mail.
     */
    public function build()
    {
        return $this->subject('Activation de votre compte')
            ->view('emails.activation')
            ->with([
                'user' => $this->user,
                'activation_code' => $this->activation_code,
                'activation_token' => $this->activation_token,
            ]);
    }
}
