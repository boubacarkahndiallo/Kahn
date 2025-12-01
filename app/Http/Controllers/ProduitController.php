<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use Illuminate\Support\Facades\Storage;
use App\Events\ProductCreated;

class ProduitController extends Controller
{
    /**
     * Affiche la liste de tous les produits.
     */
    public function index(Request $request)
    {
        $query = Produit::query();

        if ($request->has('search') && trim($request->search) !== '') {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('nom', 'LIKE', "%{$q}%")
                    ->orWhere('description', 'LIKE', "%{$q}%");
            });
        }

        if ($request->has('categorie') && trim($request->categorie) !== '') {
            $query->where('categorie', $request->categorie);
        }

        $produits = $query->orderBy('nom')->get();

        return view('produits.index', compact('produits'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $produits = Produit::where('nom', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('categorie', 'LIKE', "%{$query}%")
            ->take(5)
            ->get();

        // Ensure the image returned is a full URL so clients can use it directly
        $produits->transform(function ($p) {
            $p->image = $p->image ? Storage::url($p->image) : null;
            return $p;
        });

        return response()->json($produits);
    }

    public function legumes(Request $request)
    {
        $produits = Produit::all();
        return view('produits.categorie.legumes', compact('produits'));
    }
    public function allproduit(Request $request)
    {
        $produits = Produit::all();
        $categories = Produit::select('categorie')->distinct()->pluck('categorie');
        $selectedProduct = null;

        if ($request->has('product_id')) {
            $selectedProduct = Produit::find($request->product_id);
        }

        return view('produits.allproduit', compact('produits', 'categories', 'selectedProduct'));
    }

    /**
     * Enregistre un nouveau produit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric',
            'categorie' => 'required|string|max:255',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nom', 'description', 'prix', 'categorie', 'stock']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        $produit = Produit::create($data);

        // Créer des notifications synchrones pour que les utilisateurs les voient immédiatement
        $title = 'Nouveau produit disponible';
        $message = sprintf('%s est maintenant disponible au prix de %s GNF', $produit->nom, $produit->prix);
        $data = [
            'produit_id' => $produit->id,
            'nom' => $produit->nom,
            'prix' => $produit->prix,
            'categorie' => $produit->categorie,
            'image' => $produit->image,
        ];

        try {
            // Notifier tous les clients
            \App\Models\User::where('role', 'client')->each(function ($client) use ($title, $message, $data) {
                \App\Models\Notification::create([
                    'user_id' => $client->id,
                    'type' => 'product',
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                ]);
            });

            // Notifier les admins aussi
            \App\Models\User::whereIn('role', ['admin', 'super_admin'])->each(function ($admin) use ($title, $message, $data) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'product',
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                ]);
            });
        } catch (\Throwable $e) {
            logger()->warning('CreateProduct notifications creation failed: ' . $e->getMessage());
        }

        // Finally, broadcast the ProductCreated event (clients subscribed will receive the toast/refresh)
        try {
            event(new ProductCreated($produit));
        } catch (\Throwable $e) {
            logger()->error('ProductCreated event failed: ' . $e->getMessage());
        }

        return redirect()->route('produits.index')->with('success', 'Produit ajouté avec succès !');
    }

    /**
     * Retourne un produit en JSON pour le modal "Voir".
     */
    public function ajaxShow($id)
    {
        $produit = Produit::findOrFail($id);

        return response()->json([
            'id' => $produit->id,
            'nom' => $produit->nom,
            'categorie' => $produit->categorie,
            'statut' => $produit->statut,
            'prix' => number_format($produit->prix, 2, ',', ' ') . ' GNF',
            'stock' => $produit->stock,
            'description' => $produit->description,
            'image' => $produit->image ? asset('storage/' . $produit->image) : null,
        ]);
    }

    /**
     * Retourne un produit en JSON pour pré-remplir le formulaire du modal "Éditer".
     */
    public function ajaxEdit($id)
    {
        $produit = Produit::findOrFail($id);

        return response()->json([
            'id' => $produit->id,
            'nom' => $produit->nom,
            'categorie' => $produit->categorie,
            'statut' => $produit->statut,
            'prix' => $produit->prix,
            'stock' => $produit->stock,
            'description' => $produit->description,
            'image' => $produit->image ? asset('storage/' . $produit->image) : null,
        ]);
    }

    /**
     * Met à jour un produit.
     */
    public function update(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric',
            'categorie' => 'required|string|max:255',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nom', 'description', 'prix', 'categorie', 'stock']);

        if ($request->hasFile('image')) {
            if ($produit->image) {
                Storage::disk('public')->delete($produit->image);
            }
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        $produit->update($data);

        // Retourner le produit mis à jour et construire une URL complète pour l'image
        $updatedProduit = Produit::find($produit->id);
        $updatedProduit->image = $updatedProduit->image ? asset('storage/' . $updatedProduit->image) : null;

        return response()->json(['success' => true, 'message' => 'Produit mis à jour avec succès !', 'produit' => $updatedProduit]);
    }

    /**
     * Supprime un produit.
     */
    public function destroy(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);

        if ($produit->image) {
            Storage::disk('public')->delete($produit->image);
        }

        $produit->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Produit supprimé avec succès !']);
        }

        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès !');
    }

    /**
     * Lister les produits par catégorie.
     */
    public function category($categorie)
    {
        $produits = Produit::where('categorie', $categorie)->get();
        return view('produits.index', compact('produits'));
    }
}
