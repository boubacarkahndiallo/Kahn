<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    //La page/vue accueil.blade.php
    public function home()
    {
        // Récupère tous les produits
        $produits = Produit::all();

        // Passe la variable à la vue
        return view('Accueil.accueil', compact('produits'));
    }
    public function about()
    {
        return view('Accueil.about');
    }
    public function dashboard()
    {
        // Statistiques simples pour le tableau de bord
        $produitsCount = Produit::count();
        $commandesCount = Commande::count();
        $clientsCount = Client::count();
        $totalSales = (float) Commande::sum('prix_total');

        // Données récentes
        $recentProduits = Produit::orderBy('created_at', 'desc')->take(5)->get();
        $recentCommandes = Commande::with('client')->orderBy('date_commande', 'desc')->take(5)->get();

        // --- Données pour graphiques ---
        // Période : 7 derniers jours
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = Carbon::today()->subDays($i);
        }

        $labels7 = array_map(fn($d) => $d->format('d M'), $days);

        // Ventes par jour (somme prix_total)
        $salesByDay = [];
        foreach ($days as $d) {
            $start = $d->startOfDay();
            $end = $d->endOfDay();
            $sum = Commande::whereBetween('date_commande', [$start, $end])->sum('prix_total');
            $salesByDay[] = (float) $sum;
        }

        // Commandes par jour (counts)
        $ordersByDay = [];
        foreach ($days as $d) {
            $start = $d->startOfDay();
            $end = $d->endOfDay();
            $count = Commande::whereBetween('date_commande', [$start, $end])->count();
            $ordersByDay[] = (int) $count;
        }

        // Statut distribution
        $statuts = Commande::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();

        // Top produits (par quantité) — parcourir toutes les commandes et sommer les qty par nom
        $productCounts = [];
        Commande::select('produits')->whereNotNull('produits')->get()->each(function ($c) use (&$productCounts) {
            $items = $c->produits;
            if (!is_array($items)) return;
            foreach ($items as $it) {
                $name = $it['nom'] ?? 'Produit';
                $qty = isset($it['qty']) ? (int)$it['qty'] : (int)($it['quantite'] ?? 1);
                if (!isset($productCounts[$name])) $productCounts[$name] = 0;
                $productCounts[$name] += $qty;
            }
        });

        arsort($productCounts);
        $topProducts = array_slice($productCounts, 0, 5, true);

        return view('Accueil.dashboard', compact(
            'produitsCount',
            'commandesCount',
            'clientsCount',
            'totalSales',
            'recentProduits',
            'recentCommandes',
            'labels7',
            'salesByDay',
            'ordersByDay',
            'statuts',
            'topProducts'
        ));
    }
    public function contact()
    {
        return view('Accueil.contact');
    }
}
