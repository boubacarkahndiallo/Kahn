<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Compte Locataire Créé</title>
</head>

<body>
    <h2>Bonjour {{ $locataire->prenom }} {{ $locataire->nom }},</h2>

    <p>Votre compte locataire a été créé avec succès sur notre plateforme {{ config('app.name') }}.</p>

    <p>Voici vos informations de connexion :</p>

    <ul>
        <li><strong>Email :</strong> {{ $locataire->email }}</li>
        <li><strong>Mot de passe :</strong> {{ $password }}</li>
    </ul>

    <p>Nous vous recommandons de changer votre mot de passe lors de votre première connexion pour plus de sécurité.</p>

    <p>Vous pouvez vous connecter ici : <a href="{{ url('/login') }}">Se connecter</a></p>

    <p>Merci de votre confiance !</p>

    <p><em>{{ config('app.name') }} votre partenaire immobiler</em></p>
</body>

</html>
