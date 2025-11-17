<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckClientAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $commandeId = $request->route('id');
        if ($commandeId) {
            $commande = \App\Models\Commande::findOrFail($commandeId);

            // Vérifier que l'utilisateur est connecté
            $userId = Auth::id();
            if (!$userId) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            // Vérifier si l'utilisateur connecté est le propriétaire de la commande
            // On compare l'id du client enregistré dans la commande avec l'id de l'utilisateur connecté
            if ($commande->client_id !== $userId) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
        }

        return $next($request);
    }
}
