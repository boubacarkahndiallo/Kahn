<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    //  Affiche la liste des utilisateurs

    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }


    // Crée un nouvel utilisateur sans mot de passe

    public function store(Request $request)
    {
        try {
            // Vérifier que l'utilisateur est connecté et a le rôle admin ou super_admin
            if (!Auth::check() || !in_array(Auth::user()?->role, ['admin', 'super_admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $validator = validator($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'tel' => 'required|string|max:20|unique:users,tel',
                'whatsapp' => 'nullable|string|regex:/^\+?\d{8,15}$/',
                'email' => 'required|string|email|max:255|unique:users,email',
                'role' => 'required|in:user,admin,super_admin',
                'adresse' => 'nullable|string|max:255',
                'photo' => 'nullable|image|max:19456',
            ], [], [
                'tel.unique' => 'Ce numéro de téléphone est déjà utilisé',
                'email.unique' => 'Cette adresse email est déjà utilisée',
                'whatsapp.regex' => 'Le numéro WhatsApp doit être au format international (+XXX...)',
                'photo.max' => 'La photo ne doit pas dépasser 19 Mo.',
                'photo.image' => 'Le fichier doit être une image valide.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->only([
                'nom',
                'prenom',
                'tel',
                'whatsapp',
                'email',
                'role',
                'adresse'
            ]);

            // Si l'utilisateur courant est un admin (non super_admin), il ne peut pas créer de super_admin
            if (Auth::user()?->role === 'admin' && isset($data['role']) && $data['role'] === 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à créer un Super Admin'
                ], 403);
            }

            // Génération du mot de passe
            $password = $this->whatsAppService->generatePassword(
                $data['nom'],
                $data['prenom'],
                $data['tel']
            );
            $data['password'] = Hash::make($password);

            // Envoi du mot de passe par WhatsApp si un numéro est fourni
            if (!empty($data['whatsapp'])) {
                $message = "Bonjour {$data['prenom']}, \n\n";
                $message .= "Votre compte a été créé avec succès. \n";
                $message .= "Votre mot de passe temporaire est : $password \n\n";
                $message .= "Veuillez le changer lors de votre première connexion.";

                $sent = $this->whatsAppService->sendMessage($data['whatsapp'], $message);
                if (!$sent) {
                    Log::warning("Échec de l'envoi du message WhatsApp à l'utilisateur : {$data['whatsapp']}");
                }
            }

            // Première connexion (token unique)
            $data['first_login_token'] = Str::uuid();
            $data['has_set_password'] = false;

            // Upload photo s’il y en a une
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('users', 'public');
            }

            $user = User::create($data);
            // Recharge pour avoir l'URL de la photo
            $user = $user->fresh();

            // Génération du lien de première connexion
            $lien_first_login = route('first.login', ['token' => $data['first_login_token']]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès !',
                'user' => array_merge($user->toArray(), [
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : null
                ]),
                'lien_first_login' => $lien_first_login
            ]);
        } catch (\Throwable $e) {
            Log::error('Erreur création utilisateur : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Détails d’un utilisateur (pour modal "Voir")
     */
    public function ajaxShow($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'tel' => $user->tel,
            'whatsapp' => $user->whatsapp,
            'email' => $user->email,
            'adresse' => $user->adresse,
            'role' => $user->role,
            'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
            'has_set_password' => $user->has_set_password ? 'Oui' : 'Non',
        ]);
    }

    /**
     * Préremplissage du modal "Éditer"
     */
    public function ajaxEdit($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'tel' => $user->tel,
            'whatsapp' => $user->whatsapp,
            'email' => $user->email,
            'adresse' => $user->adresse,
            'role' => $user->role,
            'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
        ]);
    }

    /**
     * Mise à jour via AJAX
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = validator($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'tel' => 'required|string|max:20|unique:users,tel,' . $user->id,
            'whatsapp' => 'nullable|string|regex:/^\+?\d{8,15}$/',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin,super_admin',
            'adresse' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:19456',
        ], [], [
            'tel.unique' => 'Ce numéro de téléphone est déjà utilisé',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'whatsapp.regex' => 'Le numéro WhatsApp doit être au format international (+XXX...)'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'nom',
            'prenom',
            'tel',
            'whatsapp',
            'email',
            'role',
            'adresse'
        ]);

        // Si l'utilisateur courant est un admin (non super_admin), il ne peut pas définir le rôle sur admin ou super_admin
        if (Auth::user()?->role === 'admin' && isset($data['role']) && in_array($data['role'], ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à attribuer ce rôle'
            ], 403);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('users', 'public');
        }

        $user->update($data);
        // Recharge l'utilisateur pour avoir les relations et l'URL de la photo
        $user = $user->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur mis à jour avec succès !',
            'user' => array_merge($user->toArray(), [
                'photo' => $user->photo ? asset('storage/' . $user->photo) : null
            ])
        ]);
    }

    /**
     * Suppression via AJAX
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès !'
        ]);
    }
}
