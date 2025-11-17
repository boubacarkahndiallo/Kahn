<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchProducts(Request $request)
    {
        $query = $request->get('query');

        $products = Produit::where('nom', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('prix', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($products);
    }

    public function getAllProducts()
    {
        $products = Produit::all();
        return response()->json($products);
    }
}
