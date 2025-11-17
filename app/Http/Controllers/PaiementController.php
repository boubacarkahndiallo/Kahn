<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PaiementController extends Controller
{
    // Lancer un paiement selon le moyen choisi
    public function payer($factureId, $method)
    {
        switch ($method) {
            case 'orange': return $this->orangePay($factureId);
            case 'momo': return $this->momoPay($factureId);
            case 'paypal': return $this->paypalPay($factureId);
            default: abort(404, "Méthode de paiement non supportée");
        }
    }

    // ------------------- QR Code -------------------
    public function qrCode($factureId)
    {
        $url = route('paiement', ['facture' => $factureId, 'method' => 'orange']);
        $qrCode = new QrCode($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString(), 200)->header('Content-Type', 'image/png');
    }

    // ------------------- ORANGE MONEY -------------------
    private function orangePay($factureId)
    {
        $facture = $this->getFacture($factureId);

        $auth = Http::asForm()->withHeaders([
            'Authorization' => 'Basic ' . base64_encode(env('ORANGE_API_KEY') . ':' . env('ORANGE_API_SECRET')),
        ])->post('https://api.orange.com/oauth/v3/token', [
            'grant_type' => 'client_credentials',
        ]);

        $token = $auth['access_token'] ?? null;
        if (!$token) return response()->json(['error' => 'Impossible de générer un token Orange'], 500);

        $payment = Http::withToken($token)->post('https://api.orange.com/orange-money-webpay/dev/v1/webpayment', [
            "merchant_key" => env('ORANGE_MERCHANT_KEY'),
            "currency" => "GNF",
            "order_id" => $facture->id,
            "amount" => $facture->montant,
            "return_url" => route('paiement.callback', ['factureId'=>$facture->id, 'method'=>'orange']),
            "cancel_url" => route('paiement.cancel', ['factureId'=>$facture->id, 'method'=>'orange']),
            "notif_url" => route('paiement.notify', ['factureId'=>$facture->id, 'method'=>'orange']),
        ]);

        return redirect($payment['payment_url']);
    }

    public function orangeCallback(Request $request, $factureId)
    {
        Log::info("✅ Paiement Orange confirmé", $request->all());
        $this->updateFactureStatus($factureId, "payée");
        return redirect()->route('facture.show', $factureId)->with('success', 'Paiement Orange réussi ✅');
    }

    // ------------------- MTN MOMO -------------------
    private function momoPay($factureId)
    {
        $facture = $this->getFacture($factureId);

        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => env('MOMO_SUBSCRIPTION_KEY'),
            'X-Reference-Id' => (string) $facture->id,
            'Content-Type' => 'application/json',
        ])->post('https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay', [
            "amount" => $facture->montant,
            "currency" => "GNF",
            "externalId" => (string) $facture->id,
            "payer" => [
                "partyIdType" => "MSISDN",
                "partyId" => "2246xxxxxxx"
            ],
            "payerMessage" => "Paiement Facture",
            "payeeNote" => "Facture n° " . $facture->id,
        ]);

        return response()->json($response->json());
    }

    public function momoCallback(Request $request, $factureId)
    {
        Log::info("✅ Paiement MoMo confirmé", $request->all());
        $this->updateFactureStatus($factureId, "payée");
        return redirect()->route('facture.show', $factureId)->with('success', 'Paiement MTN MoMo réussi ✅');
    }

    // ------------------- PAYPAL -------------------
    private function paypalPay($factureId)
    {
        $facture = $this->getFacture($factureId);

        $response = Http::withBasicAuth(env('PAYPAL_CLIENT_ID'), env('PAYPAL_SECRET'))
            ->post('https://api-m.sandbox.paypal.com/v2/checkout/orders', [
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "reference_id" => $facture->id,
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" => $facture->montant
                    ]
                ]],
                "application_context" => [
                    "return_url" => route('paiement.callback', ['factureId'=>$facture->id, 'method'=>'paypal']),
                    "cancel_url" => route('paiement.cancel', ['factureId'=>$facture->id, 'method'=>'paypal']),
                ]
            ]);

        $approvalUrl = collect($response['links'])->firstWhere('rel','approve')['href'] ?? null;
        if ($approvalUrl) return redirect($approvalUrl);

        return response()->json(['error'=>'Impossible de générer le paiement PayPal'],500);
    }

    public function paypalCallback(Request $request, $factureId)
    {
        Log::info("✅ Paiement PayPal confirmé", $request->all());
        $this->updateFactureStatus($factureId, "payée");
        return redirect()->route('facture.show',$factureId)->with('success','Paiement PayPal réussi ✅');
    }

    // ------------------- UTILITAIRES -------------------
    private function getFacture($factureId)
    {
        return (object)[
            'id'=>$factureId,
            'client'=>'Jean Dupont',
            'montant'=>125.50,
            'date'=>now()->format('d/m/Y'),
        ];
    }

    private function updateFactureStatus($factureId, $status)
    {
        // Ici tu mets à jour ta table factures
        // Facture::where('id',$factureId)->update(['status'=>$status]);
        Log::info("📌 Facture {$factureId} mise à jour : {$status}");
    }
}
