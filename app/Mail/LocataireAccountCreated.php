<?php

namespace App\Mail;

use App\Models\Locataire;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LocataireAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $locataire;
    public $password;

    /**
     * Crée une nouvelle instance du Mailable.
     */
    public function __construct(Locataire $locataire, string $password)
    {
        $this->locataire = $locataire;
        $this->password = $password;
    }

    /**
     * Construire le message.
     */
    public function build()
    {
        return $this->subject('Votre compte locataire a été créé')
                    ->view('emails.locataire.account_created');
    }
}
