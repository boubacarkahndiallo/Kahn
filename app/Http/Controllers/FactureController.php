<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class FactureController extends Controller
{
    // 🔹 Afficher la facture
    public function show($factureId)
    {
        $facture = $this->getFacture($factureId);
        $qrCodeBase64 = $this->generateQrCode($facture);

        return view('facture.show', [
            'facture' => $facture,
            'qrCode'  => $qrCodeBase64,
        ]);
    }

    // 🔹 Télécharger la facture en PDF
    public function download($factureId)
    {
        $facture = $this->getFacture($factureId);
        $qrCodeBase64 = $this->generateQrCode($facture);

        $pdf = Pdf::loadView('facture.pdf', [
            'facture' => $facture,
            'qrCode' => $qrCodeBase64
        ]);

        return $pdf->download("facture-{$facture->id}.pdf");
    }

    // 🔹 Ouvrir le PDF dans le navigateur
    public function preview($factureId)
    {
        $facture = $this->getFacture($factureId);
        $qrCodeBase64 = $this->generateQrCode($facture);

        $pdf = Pdf::loadView('facture.pdf', [
            'facture' => $facture,
            'qrCode' => $qrCodeBase64
        ]);

        return $pdf->stream("facture-{$facture->id}.pdf");
    }

    // 🔹 Simulation d’une facture
    private function getFacture($factureId)
    {
        return (object) [
            'id' => $factureId,
            'client' => 'Jean Dupont',
            'montant' => 125.50,
            'date' => now()->format('d/m/Y'),
            'adresse' => '123 Rue des Immeubles, Conakry, Guinée',
            'gps' => '9.6412° N, 13.5784° W',
        ];
    }


    // 🔹 Générer le QR Code avec toutes les infos de la facture
    private function generateQrCode($facture)
    {
        // Infos à encoder
        $data = "FACTURE N° {$facture->id}\n"
            . "Client: {$facture->client}\n"
            . "Montant: " . number_format($facture->montant, 2, ',', ' ') . " €\n"
            . "Date: {$facture->date}\n"
            . "Adresse: {$facture->adresse}\n"
            . "Localisation GPS: {$facture->gps}\n";

        // Création du QR code
        $qrCode = new QrCode(
            data: $data,
            size: 200,
            margin: 10
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return base64_encode($result->getString());
    }
}
