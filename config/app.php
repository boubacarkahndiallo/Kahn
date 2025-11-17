<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nom de l'application
    |--------------------------------------------------------------------------
    |
    | Ce nom sera utilisé dans les notifications, les emails et
    | autres contextes où le nom de l'application doit apparaître.
    |
    */

    'name' => env('APP_NAME', 'MyAPI'),

    /*
    |--------------------------------------------------------------------------
    | Informations Email (optionnel pour API)
    |--------------------------------------------------------------------------
    |
    | Tu peux définir ici des variables liées à l'envoi des emails.
    | Cela sera utile pour la vérification des comptes, mot de passe oublié, etc.
    |
    */

    'mail_host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
    'mail_port' => env('MAIL_PORT', 2525),
    'mail_username' => env('MAIL_USERNAME', ''),
    'mail_password' => env('MAIL_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Environnement de l'application
    |--------------------------------------------------------------------------
    |
    | Définit si ton API tourne en local, en staging ou en production.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Mode debug
    |--------------------------------------------------------------------------
    |
    | Si activé, Laravel renvoie les détails complets des erreurs.
    | À désactiver en production pour plus de sécurité.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL de l'application
    |--------------------------------------------------------------------------
    |
    | Sert pour la génération des liens dans les mails, notifications et API.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Fuseau horaire
    |--------------------------------------------------------------------------
    |
    | Définit la timezone utilisée par PHP et Laravel.
    | Pour la Guinée : "Africa/Conakry"
    |
    */

    'timezone' => env('APP_TIMEZONE', 'Africa/Conakry'),

    /*
    |--------------------------------------------------------------------------
    | Langues et fallback
    |--------------------------------------------------------------------------
    |
    | Définit la langue principale et la langue de secours.
    | Utile si ton API doit renvoyer des messages localisés.
    |
    */

    'locale' => env('APP_LOCALE', 'fr'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'fr_FR'),

    /*
    |--------------------------------------------------------------------------
    | Clé d'encryption
    |--------------------------------------------------------------------------
    |
    | Sert à sécuriser les tokens et données sensibles.
    | Doit être défini dans le fichier .env : APP_KEY
    |
    */

    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | Configure le driver utilisé pour gérer le mode maintenance.
    | "cache" recommandé pour les API distribuées.
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
