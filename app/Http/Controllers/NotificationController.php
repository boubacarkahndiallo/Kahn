<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Afficher la page des notifications
     */
    public function index()
    {
        return view('notifications.index');
    }

    /**
     * Récupérer les notifications non lues de l'utilisateur actuel
     */
    public function getUnread()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifs = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json(['notifications' => $notifs]);
    }

    // Route alias: /notifications/list
    public function list(Request $request): JsonResponse
    {
        return $this->getUnread();
    }

    /**
     * Alias used by route /notifications/unread-count
     */
    public function unreadCount(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $count = Notification::where('user_id', $user->id)->whereNull('read_at')->count();
        return response()->json(['unread_count' => $count]);
    }

    /**
     * Récupérer toutes les notifications (avec pagination)
     */
    public function getAll(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $perPage = $request->get('per_page', 20);
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($notifications);
    }

    // Optional alias to support future API naming
    public function listAll(Request $request): JsonResponse
    {
        return $this->getAll($request);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = Notification::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    // Route alias: POST /notifications/{id}/read
    public function markAsReadRoute(Request $request, $id): JsonResponse
    {
        return $this->markAsRead($id);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Notification::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    // Route alias: POST /notifications/read-all
    public function markAllRead(Request $request): JsonResponse
    {
        return $this->markAllAsRead();
    }

    /**
     * Supprimer une notification
     */
    public function delete($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Notification::where('id', $id)->where('user_id', $user->id)->delete();
        return response()->json(['success' => true]);
    }
}
