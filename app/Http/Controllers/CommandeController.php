<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Client;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;
use App\Events\OrderCreated;
use App\Jobs\SendOrderDeliveryConfirmationJob;
use App\Jobs\SendOrderToAdminWhatsAppJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class CommandeController extends Controller
{
    /**
     * ✅ Affichage de la liste des commandes avec recherche
     */
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $user = Auth::user();

        // Guard: si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (!$user) {
            return redirect()->route('login');
        }

        // Si l'utilisateur est admin (role != 'client'/'fournisseur'), afficher TOUTES les commandes
        // Sinon, afficher uniquement ses propres commandes
        $isAdmin = !in_array($user->role, ['client', 'fournisseur']);

        $commandes = Commande::with('client');

        if ($user && !$isAdmin) {
            // Filtrer par client_id de l'utilisateur connecté
            $commandes = $commandes->where('client_id', $user->id);
        }

        // Appliquer la recherche si elle existe
        if ($search) {
            $commandes = $commandes->where(function ($query) use ($search) {
                $query->where('numero_commande', 'like', "%$search%")
                    ->orWhere('produits', 'like', "%$search%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('nom', 'like', "%$search%");
                    });
            });
        }

        $commandes = $commandes->orderBy('created_at', 'desc')->get();

        return view('commandes.index', compact('commandes'));
    }

    /**
     * ✅ Création d'une commande (AJAX)
     */
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @var \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        // Accept produits sent as JSON string (from some AJAX forms) or as array.
        if ($request->has('produits') && is_string($request->produits)) {
            $decoded = json_decode($request->produits, true);
            if (is_array($decoded)) {
                $request->merge(['produits' => $decoded]);
            }
        }

        // normalize product keys: accept 'quantite' (FR) or 'qty' (EN)
        if ($request->has('produits') && is_array($request->produits)) {
            $normalized = [];
            foreach ($request->produits as $p) {
                // ensure keys qty/prix/nom exist with expected names
                $normalized[] = [
                    'produit_id' => $p['produit_id'] ?? $p['id'] ?? null,
                    'nom' => $p['nom'] ?? $p['name'] ?? null,
                    'qty' => $p['qty'] ?? $p['quantite'] ?? $p['quantity'] ?? null,
                    'prix' => $p['prix'] ?? $p['price'] ?? null,
                ];
            }
            // replace request produits temporarily for validation
            $request->merge(['produits' => $normalized]);
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|integer|exists:produits,id',
            'produits.*.nom' => 'required|string',
            'produits.*.qty' => 'required|integer|min:1', // quantité normalisée
            'produits.*.prix' => 'required|numeric|min:0',
            'prix_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en_cours,livree,annulee',
        ]);

        $client = Client::findOrFail($request->client_id);

        // Idempotency token from client (optional)
        $clientOrderUuid = $request->input('client_order_uuid') ?? null;

        // Merge with existing draft commande (created previously with prix_total=0 and produits empty)
        // to avoid creating duplicate orders when a draft was created earlier (e.g. during client registration)
        if (empty($clientOrderUuid)) {
            $draft = Commande::where('client_id', $client->id)
                ->where('statut', 'en_cours')
                ->where(function ($q) {
                    $q->whereNull('produits')->orWhere('prix_total', 0)->orWhereRaw("JSON_LENGTH(COALESCE(produits, '[]')) = 0");
                })->orderBy('created_at', 'desc')->first();

            if ($draft) {
                // Update draft instead of creating a new commande
                $draft->produits = $request->produits;
                $draft->prix_total = $request->prix_total;
                $draft->statut = $request->statut;
                $draft->date_commande = now();
                $draft->client_order_uuid = $clientOrderUuid;
                $draft->save();

                // ensure numero_commande exists
                if (empty($draft->numero_commande)) {
                    $draft->numero_commande = 'CMD-' . str_pad($draft->id, 3, '0', STR_PAD_LEFT);
                    $draft->save();
                }

                $commande = $draft;

                // respond as if created (idempotent)
                $responsePayload = [
                    'success' => true,
                    'message' => 'Commande mise à jour depuis un brouillon existant.',
                    'commande' => [
                        'id' => $commande->id,
                        'numero_commande' => $commande->numero_commande,
                        'client_id' => $commande->client_id,
                        'client_nom' => $client->nom,
                        'produits' => $commande->produits,
                        'prix_total' => $commande->prix_total,
                        'statut' => $commande->statut,
                        'date_commande' => $commande->date_commande->format('d/m/Y H:i'),
                    ],
                ];

                if ($request->wantsJson() || $request->ajax() || str_contains($request->header('Accept') ?? '', 'application/json')) {
                    return response()->json($responsePayload);
                }

                return redirect()->route('commandes.index')->with('success', $responsePayload['message']);
            }
        }
        // Idempotency: if client provides `client_order_uuid`, check for existing commande
        $clientOrderUuid = $request->input('client_order_uuid') ?? null;
        if ($clientOrderUuid) {
            $existing = Commande::where('client_order_uuid', $clientOrderUuid)->first();
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'message' => 'Commande déjà enregistrée',
                    'commande' => [
                        'id' => $existing->id,
                        'numero_commande' => $existing->numero_commande,
                        'client_id' => $existing->client_id,
                        'client_nom' => $client->nom,
                        'produits' => $existing->produits,
                        'prix_total' => $existing->prix_total,
                        'statut' => $existing->statut,
                        'date_commande' => $existing->date_commande->format('d/m/Y H:i'),
                    ],
                ]);
            }
        }

        // Vérifier la disponibilité du stock pour chaque produit commandé
        foreach ($request->produits as $p) {
            $prod = Produit::find($p['produit_id']);
            if ($prod) {
                $available = (int)$prod->stock;
                $qty = (int)$p['qty'];
                if ($qty > $available) {
                    return response()->json([
                        'success' => false,
                        'message' => "Quantité demandée non disponible pour \"{$prod->nom}\" (stock: {$available})",
                    ], 422);
                }
            }
        }

        // Créer la commande sans dépendre d'un calcul non-atomique du numéro.
        // Nous générerons ensuite `CMD-###` à partir de l'ID auto-incrémenté,
        // ce qui évite les collisions concurrentes.
        $commande = Commande::create([
            'client_id' => $client->id,
            'produits' => $request->produits, // casté en JSON par le modèle
            'prix_total' => $request->prix_total,
            'statut' => $request->statut,
            'date_commande' => now(),
            'client_order_uuid' => $clientOrderUuid,
        ]);

        // Générer un numéro lisible et séquentiel basé sur l'ID (CMD-001, CMD-002, ...)
        $commande->numero_commande = 'CMD-' . str_pad($commande->id, 3, '0', STR_PAD_LEFT);
        $commande->save();

        // Envoi WhatsApp immédiat à l'admin (tentative synchrone + job en arrière-plan si échec)
        try {
            // Prefer the explicit configuration from config/services.php and fallback to env var
            $adminNumber = config('services.admin.whatsapp_number') ?? env('ADMIN_WHATSAPP_NUMBER', '+224623248567');
            $produitsList = collect($commande->produits)->map(function ($p) {
                return ($p['nom'] ?? '') . ' x' . ($p['qty'] ?? 1) . ' (' . ($p['prix'] ?? 0) . ' GNF)';
            })->implode(', ');
            $message = "Nouvelle commande reçue !\n" .
                "Numéro : " . $commande->numero_commande . "\n" .
                "Client : " . $client->nom . "\n" .
                "Téléphone : " . ($client->tel ?? '-') . "\n" .
                "Produits : " . $produitsList . "\n" .
                "Total : " . $commande->prix_total . " GNF";

            $sent = app(\App\Services\WhatsAppService::class)->sendMessage($adminNumber, $message);

            // Si l'envoi synchrone échoue, dispatcher une job pour réessayer en background
            if (!$sent) {
                SendOrderToAdminWhatsAppJob::dispatch($commande->id);
            }
        } catch (\Throwable $e) {
            logger()->error('Envoi WhatsApp admin échoué: ' . $e->getMessage());
            // fallback: dispatcher la job pour réessayer
            try {
                SendOrderToAdminWhatsAppJob::dispatch($commande->id);
            } catch (\Throwable $ex) {
                logger()->error('Dispatch SendOrderToAdminWhatsAppJob failed: ' . $ex->getMessage());
            }
        }
        // Créer des notifications synchrones pour une visibilité immédiate
        try {
            $title = 'Nouvelle commande reçue';
            $message = sprintf('Commande #%s de %s - Total: %s GNF', $commande->numero_commande, $client->nom ?? 'Client', $commande->prix_total);
            $data = [
                'commande_id' => $commande->id,
                'numero_commande' => $commande->numero_commande,
                'client_id' => $client->id,
                'client_nom' => $client->nom ?? null,
                'client_tel' => $client->tel ?? null,
                'prix_total' => $commande->prix_total,
                'produits' => $commande->produits,
                'date_commande' => $commande->date_commande?->toIso8601String(),
            ];

            // Notifier admins
            \App\Models\User::whereIn('role', ['admin', 'super_admin'])->each(function ($admin) use ($title, $message, $data) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'order',
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                ]);
            });

            // Notifier le client
            if ($client) {
                \App\Models\Notification::create([
                    'user_id' => $client->id,
                    'type' => 'order',
                    'title' => 'Votre commande a été reçue',
                    'message' => 'Votre commande ' . $commande->numero_commande . ' a bien été enregistrée. Nous vous contacterons sous peu.',
                    'data' => $data,
                ]);
            }
        } catch (\Throwable $e) {
            logger()->warning('CreateOrder notifications creation failed: ' . $e->getMessage());
        }

        // Broadcast event to notify admins in real-time
        try {
            event(new OrderCreated($commande));
        } catch (\Throwable $e) {
            // Do not block the main flow if broadcasting fails; log for debugging
            logger()->error('Broadcast OrderCreated failed: ' . $e->getMessage());
        }

        // Dispatch a queued job to ask the client (via WhatsApp) 1 hour after order creation
        try {
            SendOrderDeliveryConfirmationJob::dispatch($commande->id)->delay(now()->addHour());
        } catch (\Throwable $e) {
            logger()->error('Dispatch SendOrderDeliveryConfirmationJob failed: ' . $e->getMessage());
        }

        // Réduire le stock pour chaque produit commandé (opération atomique)
        try {
            DB::transaction(function () use ($request) {
                foreach ($request->produits as $p) {
                    $produitId = $p['produit_id'] ?? null;
                    $qty = (int)($p['qty'] ?? 0);
                    if ($produitId && $qty > 0) {
                        \App\Models\Produit::where('id', $produitId)->decrement('stock', $qty);
                    }
                }
            });
        } catch (\Throwable $e) {
            logger()->error('Erreur réduction stock: ' . $e->getMessage());
            // ne pas bloquer la commande si la réduction échoue; log pour investigation
        }

        // Répondre différemment selon le type de requête (AJAX/json ou formulaire classique)
        $responsePayload = [
            'success' => true,
            'message' => 'Un agent vous contactera dans quelques minutes. Vous pouvez modifier votre commande dans le panier si nécessaire.',
            'commande' => [
                'id' => $commande->id,
                'numero_commande' => $commande->numero_commande,
                'client_id' => $client->id,
                'client_nom' => $client->nom,
                'produits' => $commande->produits,
                'prix_total' => $commande->prix_total,
                'statut' => $commande->statut,
                'date_commande' => $commande->date_commande->format('d/m/Y H:i'),
            ],
        ];

        if ($request->wantsJson() || $request->ajax() || str_contains($request->header('Accept') ?? '', 'application/json')) {
            return response()->json($responsePayload);
        }

        // Si requête HTML classique (formulaire), rediriger vers la liste avec message flash
        return redirect()->route('commandes.index')->with('success', $responsePayload['message']);
    }

    /**
     * Détails d'une commande (pour modal Voir)
     */
    /**
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * @var string $id
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        $commande = Commande::with('client')->findOrFail($id);

        return response()->json([
            'success' => true,
            'commande' => [
                'id' => $commande->id,
                'numero_commande' => $commande->numero_commande,
                'client_id' => $commande->client_id,
                'client_nom' => $commande->client->nom,
                'produits' => $commande->produits,
                'prix_total' => $commande->prix_total,
                'statut' => $commande->statut,
                'date_commande' => $commande->date_commande->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Récupération des données d'édition (pour modal Modifier)
     */
    /**
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * @var string $id
     */
    public function edit(string $id): \Illuminate\Http\JsonResponse
    {
        $commande = Commande::with('client')->findOrFail($id);

        return response()->json([
            'success' => true,
            'commande' => $commande,
            'client' => $commande->client,
        ]);
    }

    /**
     * Mise à jour d'une commande (AJAX)
     */
    /**
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * @var \Illuminate\Http\Request $request
     * @var string $id
     */
    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $commande = Commande::findOrFail($id);

        // Si seulement le statut est envoyé (depuis le modal de modification)
        if ($request->has('statut')) {
            $request->validate([
                'statut' => 'required|in:en_cours,livree,annulee',
            ]);

            $commande->update([
                'statut' => $request->statut,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Commande mise à jour avec succès',
                'commande' => $commande,
            ]);
        }

        // Sinon, validation complète (pour mise à jour complète future)
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'produits' => 'required|array|min:1',
            'produits.*.nom' => 'required|string',
            'produits.*.qty' => 'required|integer|min:1',
            'produits.*.prix' => 'required|numeric|min:0',
            'prix_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en_cours,livree,annulee',
        ]);

        $client = Client::findOrFail($request->client_id);

        $commande->update([
            'client_id' => $client->id,
            'produits' => $request->produits,
            'prix_total' => $request->prix_total,
            'statut' => $request->statut,
        ]);

        return response()->json([
            'success' => true,
            'commande' => [
                'id' => $commande->id,
                'numero_commande' => $commande->numero_commande,
                'client_id' => $client->id,
                'client_nom' => $client->nom,
                'produits' => $commande->produits,
                'prix_total' => $commande->prix_total,
                'statut' => $commande->statut,
                'date_commande' => $commande->date_commande->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Suppression d'une commande avec redirection
     */
    /**
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     * @var string $id
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $commande = Commande::findOrFail($id);
            $commande->delete();

            if ($request->wantsJson() || $request->ajax() || str_contains($request->header('Accept') ?? '', 'application/json')) {
                return response()->json(['success' => true, 'message' => 'La commande a été annulée avec succès.']);
            }

            return redirect()->route('commandes.index')
                ->with('success', 'La commande a été supprimée avec succès ✅');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax() || str_contains($request->header('Accept') ?? '', 'application/json')) {
                return response()->json(['success' => false, 'message' => 'Une erreur est survenue lors de la suppression de la commande'], 500);
            }
            return redirect()->route('commandes.index')
                ->with('error', 'Une erreur est survenue lors de la suppression de la commande');
        }
    }

    /**
     * Liste des commandes du client connecté (API)
     */
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @var \Illuminate\Http\Request $request
     */
    public function mesCommandes(Request $request): \Illuminate\Http\JsonResponse
    {
        $client_id = auth('web')->id(); // Récupère l'ID du client connecté

        $commandes = Commande::where('client_id', $client_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($commande) {
                return [
                    'id' => $commande->id,
                    'numero_commande' => $commande->numero_commande,
                    'date_commande' => $commande->date_commande_format,
                    'statut' => $commande->statut,
                    'produits_description' => $commande->produits_description,
                    'total_format' => $commande->total_format
                ];
            });

        return response()->json(['commandes' => $commandes]);
    }
}
