<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement Facture #{{ $facture->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; text-align: center; padding: 50px; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: inline-block; }
        h2 { margin-bottom: 20px; }
        button { padding: 10px 20px; border: none; border-radius: 8px; margin: 10px; cursor: pointer; font-size: 16px; }
        .om { background: orange; color: white; }
        .momo { background: purple; color: white; }
        .paypal { background: #0070ba; color: white; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Paiement de la facture #{{ $facture->id }}</h2>
        <p>Montant : <strong>{{ number_format($facture->montant, 2, ',', ' ') }} €</strong></p>

        <form action="{{ url('/paiement/' . $facture->id . '/process') }}" method="POST">
            @csrf
            <button type="submit" name="method" value="om" class="om">Orange Money</button>
            <button type="submit" name="method" value="momo" class="momo">Mobile Money</button>
            <button type="submit" name="method" value="paypal" class="paypal">PayPal</button>
        </form>
    </div>
</body>
</html>
