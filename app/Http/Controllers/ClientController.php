<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Commande;
use App\Traits\PhoneNumberValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    use PhoneNumberValidator;
    /**
     * Affichage de la liste des clients
     */
    public function index()
    {
        $clients = Client::orderBy('created_at', 'desc')->get();
        return view('clients.index', compact('clients'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Enregistrement d'un nouveau client
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'tel' => 'required|string|max:20',
            'tel_e164' => 'nullable|string|max:30',
            'tel_e164' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:20',
            'whatsapp_e164' => 'nullable|string|max:30',
            'whatsapp_e164' => 'nullable|string|max:30',
            'adresse' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'statut' => 'nullable|in:actif,inactif',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Prefer E164 inputs if provided (client-side formatting)
        $inputTel = $request->input('tel_e164') ?: $validated['tel'];
        $inputWhatsapp = $request->input('whatsapp_e164') ?: ($validated['whatsapp'] ?? null);

        // Valider le téléphone
        if (!$this->validatePhoneNumber($inputTel)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Numéro de téléphone invalide'
                ], 422);
            }
            return back()->withErrors(['tel' => 'Numéro de téléphone invalide']);
        }

        // Normaliser le téléphone au format E164
        $normalized_tel = $this->formatPhoneNumber($inputTel);
        if (!$normalized_tel) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Impossible de formater le numéro de téléphone'
                ], 422);
            }
            return back()->withErrors(['tel' => 'Impossible de formater le numéro de téléphone']);
        }

        $validated['tel'] = $normalized_tel;

        // Vérifier l'unicité du téléphone normalisé
        if (Client::where('tel', $normalized_tel)->exists()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'code' => 'exists',
                    'message' => 'Ce numéro est déjà enregistré'
                ], 409);
            }
            return back()->withErrors(['tel' => 'Ce numéro est déjà enregistré']);
        }

        // Upload image si fournie
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('clients', 'public');
        }

        // Statut par défaut
        $validated['statut'] = $validated['statut'] ?? 'actif';

        // Normaliser et set whatsapp
        if ($inputWhatsapp) {
            $normalized_whatsapp = $this->formatPhoneNumber($inputWhatsapp);
            $validated['whatsapp'] = $normalized_whatsapp ?: $inputWhatsapp;
        }

        // Création du client
        $client = Client::create($validated);

        // Note: ne plus créer une commande vide ici — la commande doit être créée
        // seulement lorsque le client confirme réellement son panier / passe sa commande.

        // Stocker l'ID du dernier client dans la session
        session(['last_client_id' => $client->id]);

        // Réponse AJAX (formulaire modal)
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'client' => [
                    'id' => $client->id,
                    'nom' => $client->nom,
                    'tel' => $client->tel,
                    'whatsapp' => $client->whatsapp,
                    'adresse' => $client->adresse,
                    'latitude' => $client->latitude,
                    'longitude' => $client->longitude,
                    'statut' => $client->statut,
                    'description' => $client->description,
                    'image' => $client->image ? asset('storage/' . $client->image) : null,
                ],
                // plus de création de commande automatique ici
            ]);
        }

        // Si c'est une requête classique
        return redirect()->route('produits.allproduit')
            ->with('success', 'Inscription réussie ! Vos informations ont été enregistrées.');
    }

    /**
     * Récupère le dernier client enregistré (pour le panier, facture, etc.)
     */
    public function getLastClient()
    {
        $lastClientId = session('last_client_id');

        if (!$lastClientId) {
            $client = Client::latest()->first();
        } else {
            $client = Client::find($lastClientId);
        }

        if (!$client) {
            return response()->json(['error' => 'Aucun client enregistré'], 404);
        }

        return response()->json([
            'id' => $client->id,
            'nom' => $client->nom,
            'tel' => $client->tel,
            'whatsapp' => $client->whatsapp,
            'adresse' => $client->adresse,
            'latitude' => $client->latitude,
            'longitude' => $client->longitude,
            'statut' => $client->statut,
            'description' => $client->description,
            'image' => $client->image ? asset('storage/' . $client->image) : null,
        ]);
    }

    /**
     * Affichage d’un client
     */
    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        return view('clients.show', compact('client'));
    }

    /**
     * Formulaire d’édition
     */
    public function edit(string $id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Mise à jour d'un client
     */
    public function update(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'tel' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'statut' => 'required|in:actif,inactif',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Prefer E164 inputs if provided
        $inputTel = $request->input('tel_e164') ?: $validated['tel'];
        $inputWhatsapp = $request->input('whatsapp_e164') ?: ($validated['whatsapp'] ?? null);

        // Valider et normaliser le téléphone
        if (!$this->validatePhoneNumber($inputTel)) {
            return back()->withErrors(['tel' => 'Numéro de téléphone invalide']);
        }

        $normalized_tel = $this->formatPhoneNumber($inputTel);
        if (!$normalized_tel) {
            return back()->withErrors(['tel' => 'Impossible de formater le numéro de téléphone']);
        }

        // Vérifier l'unicité si le numéro a changé
        if ($normalized_tel !== $client->tel) {
            if (Client::where('tel', $normalized_tel)->exists()) {
                return back()->withErrors(['tel' => 'Ce numéro est déjà enregistré']);
            }
        }

        $validated['tel'] = $normalized_tel;

        if ($inputWhatsapp) {
            $normalizedWhatsapp = $this->formatPhoneNumber($inputWhatsapp);
            if ($normalizedWhatsapp) {
                $validated['whatsapp'] = $normalizedWhatsapp;
            } else {
                $validated['whatsapp'] = $inputWhatsapp;
            }
        }

        // Mise à jour de l'image
        if ($request->hasFile('image')) {
            if ($client->image && Storage::disk('public')->exists($client->image)) {
                Storage::disk('public')->delete($client->image);
            }
            $validated['image'] = $request->file('image')->store('clients', 'public');
        }

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Suppression d’un client
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);

        if ($client->image && Storage::disk('public')->exists($client->image)) {
            Storage::disk('public')->delete($client->image);
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }

    /**
     * Affichage AJAX pour le modal "Voir Client"
     */
    public function ajaxShow(string $id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client non trouvé'], 404);
        }

        return response()->json([
            'id' => $client->id,
            'nom' => $client->nom,
            'tel' => $client->tel,
            'whatsapp' => $client->whatsapp,
            'adresse' => $client->adresse,
            'latitude' => $client->latitude,
            'longitude' => $client->longitude,
            'statut' => $client->statut,
            'description' => $client->description,
            'image' => $client->image ? asset('storage/' . $client->image) : null,
        ]);
    }

    /**
     * Affichage AJAX pour le modal "Éditer Client"
     */
    public function ajaxEdit(string $id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client non trouvé'], 404);
        }

        return response()->json([
            'id' => $client->id,
            'nom' => $client->nom,
            'tel' => $client->tel,
            'whatsapp' => $client->whatsapp,
            'adresse' => $client->adresse,
            'latitude' => $client->latitude,
            'longitude' => $client->longitude,
            'statut' => $client->statut,
            'description' => $client->description,
            'image' => $client->image ? asset('storage/' . $client->image) : null,
        ]);
    }
}
