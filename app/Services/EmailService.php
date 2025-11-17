<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    protected $app_name;
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $encryption;

    public function __construct()
    {
        $this->app_name  = config('app.name');
        $this->host      = config('mail.mailers.smtp.host');
        $this->port      = config('mail.mailers.smtp.port');
        $this->username  = config('mail.mailers.smtp.username');
        $this->password  = config('mail.mailers.smtp.password');
        $this->encryption = config('mail.mailers.smtp.encryption', 'tls');
    }

    public function sendEmail($subject, $emailUser, $nameUser, $activation_code, $activation_token)
    {
        $mail = new PHPMailer(true);

        try {
            // Activer le debug SMTP pour voir les détails
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function ($str, $level) {
                echo "📧 [SMTP Debug] $str\n";
            };

            // Paramètres SMTP
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->Port       = $this->port;
            $mail->SMTPSecure = $this->encryption;

            // Expéditeur
            $mail->setFrom(config('mail.from.address'), $this->app_name);
            $mail->addReplyTo(config('mail.from.address'), $this->app_name);

            // Destinataire
            $mail->addAddress($emailUser, $nameUser);

            // Contenu HTML
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $this->renderEmail($nameUser, $activation_code, $activation_token);

            if ($mail->send()) {
                echo "✅ Email envoyé avec succès à $emailUser";
                return true;
            } else {
                echo "❌ Échec d'envoi";
                return false;
            }
        } catch (Exception $e) {
            echo "⚠️ Erreur d'envoi : {$mail->ErrorInfo}";
            return false;
        }
    }

    protected function renderEmail($name, $activation_code, $activation_token)
    {
        return view('mail.confirmation_email', [
            'name'            => $name,
            'activation_code' => $activation_code,
            'activation_token' => $activation_token,
        ])->render();
    }
}
