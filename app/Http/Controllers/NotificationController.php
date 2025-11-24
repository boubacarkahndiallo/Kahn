<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Page listing (optional)
    public function index()
    {
        return view('notifications.index');
    }

    // Liste les notifications de l'utilisateur connecté (limit 50)
    public function list(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $notifs = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json(['notifications' => $notifs]);
    }

    // Récupérer le nombre de non-lues
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $count = Notification::where('user_id', $user->id)->whereNull('read_at')->count();
        return response()->json(['unread_count' => $count]);
    }

    // Marque une notification comme lue
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $notification = Notification::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    // Marque toutes comme lues
    public function markAllRead(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        Notification::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    // Supprimer
    public function delete(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        Notification::where('id', $id)->where('user_id', $user->id)->delete();
        return response()->json(['success' => true]);
    }
}
