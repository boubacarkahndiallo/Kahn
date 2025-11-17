<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="x-apple-disable-message-reformatting" />
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no" />
    <title>Activation de votre compte</title>
    <style>
        /* Responsive */
        @media only screen and (max-width:600px) {
            .container {
                width: 100% !important;
            }

            .p-24 {
                padding: 20px !important;
            }

            .h1 {
                font-size: 24px !important;
                line-height: 1.3 !important;
            }

            .btn {
                display: block !important;
                width: 100% !important;
            }
        }

        /* Mode sombre */
        @media (prefers-color-scheme: dark) {
            .bg {
                background: #0b0d10 !important;
            }

            .card {
                background: #161a1d !important;
                border-color: #283038 !important;
            }

            .text {
                color: #e5e7eb !important;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background:#f4f5f7;">
    <div role="article" aria-roledescription="email" aria-label="Activation de compte" lang="fr" dir="ltr"
        class="bg" style="background:#f4f5f7;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
            style="border-collapse:collapse;">
            <tr>
                <td align="center" style="padding:24px;">
                    <!-- Wrapper -->
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600"
                        class="container card"
                        style="width:600px; max-width:600px; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,.06);">

                        <!-- Bandeau couleur -->
                        <tr>
                            <td height="6" style="line-height:6px; font-size:0;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    border="0">
                                    <tr>
                                        <td style="background:#e11d48; height:6px;">&nbsp;</td>
                                        <td style="background:#f59e0b; height:6px;">&nbsp;</td>
                                        <td style="background:#10b981; height:6px;">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Logo -->
                        <tr>
                            <td align="center" style="padding:28px 24px 0 24px;">
                                <img src="{{ asset('images/logo.png') }}" width="64" height="64"
                                    alt="{{ config('app.name') }}"
                                    style="display:block; border:0; outline:none; text-decoration:none; border-radius:12px;" />
                                <div style="height:12px;"></div>
                            </td>
                        </tr>

                        <!-- Titre -->
                        <tr>
                            <td class="p-24" style="padding:8px 32px 0 32px;">
                                <h1 class="h1 text"
                                    style="margin:0; font-family:Arial, Helvetica, sans-serif; font-size:28px; line-height:1.25; color:#111827;">
                                  Bonjour {{ $user()->name }}, activez votre compte !
                                </h1>
                            </td>
                        </tr>

                        <!-- Message -->
                        <tr>
                            <td class="p-24" style="padding:16px 32px 0 32px;">
                                <p class="text"
                                    style="margin:0; font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:1.6; color:#374151;">
                                    Merci de vous être inscrit sur <strong>{{ config('app.name') }}</strong> !
                                    Pour finaliser votre inscription, utilisez le code d’activation ci-dessous ou
                                    cliquez
                                    sur le bouton.
                                </p>
                            </td>
                        </tr>

                        <!-- Code d'activation -->
                        <tr>
                            <td style="padding:16px 32px;">
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                                    style="border-collapse:separate;">
                                    <tr>
                                        <td class="text"
                                            style="background:#111827; color:#ffffff; font-family:Consolas, Menlo, Monaco, 'Courier New', monospace; font-size:22px; letter-spacing:3px; text-align:center; padding:16px 12px; border-radius:10px;">
                                            {{ $activation_code }}
                                        </td>
                                    </tr>
                                </table>
                                <div style="height:8px;"></div>
                                <p class="text"
                                    style="margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:1.6; color:#6b7280; text-align:center;">
                                    Ce code expire dans <strong>15 minutes</strong>.
                                </p>
                            </td>
                        </tr>

                        <!-- Bouton de confirmation -->
                        <tr>
                            <td align="center" style="padding:8px 32px 24px 32px;">
                                <a class="btn"
                                    href="{{ route('app_activation_account_link', ['token' => $activation_token]) }}"
                                    target="_blank"
                                    style="background:#10b981; border:1px solid #065f46; color:#ffffff; display:inline-block; font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:700; line-height:48px; text-align:center; text-decoration:none; border-radius:10px; padding:0 28px;">
                                    Confirmer mon compte
                                </a>
                                <div style="height:12px;"></div>
                                <p class="text"
                                    style="margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:1.6; color:#6b7280;">
                                    Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :
                                    <br>
                                    <span style="word-break:break-all; color:#065f46;">
                                        {{ route('app_activation_account_link', ['token' => $activation_token]) }}
                                    </span>
                                </p>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style="padding:16px 32px 28px 32px;">
                                <p class="text"
                                    style="margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:1.6; color:#6b7280;">
                                    Cordialement,<br>
                                    L’équipe <strong>{{ config('app.name') }}</strong>
                                </p>
                                <div style="height:12px;"></div>
                                <p class="text"
                                    style="margin:0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:1.6; color:#9ca3af;">
                                    Si vous n’êtes pas à l’origine de cette inscription, ignorez cet email.
                                </p>
                            </td>
                        </tr>

                    </table>
                    <!-- /Wrapper -->
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
