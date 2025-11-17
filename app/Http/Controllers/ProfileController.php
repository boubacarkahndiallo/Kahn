<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\PhoneNumberValidator;

class ProfileController extends Controller
{
    use PhoneNumberValidator;

    /**
     * Update the current user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'prenom' => 'nullable|string|max:50',
            'nom' => 'nullable|string|max:50',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'tel' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'adresse' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:4|confirmed',
        ]);

        // Update simple fields
        $user->prenom = $validated['prenom'] ?? $user->prenom;
        $user->nom = $validated['nom'] ?? $user->nom;
        $user->email = $validated['email'] ?? $user->email;
        $user->adresse = $validated['adresse'] ?? $user->adresse;

        // Normalize phone numbers when possible
        if ($request->filled('tel')) {
            $formatted = $this->formatPhoneNumber($request->input('tel'));
            $user->tel = $formatted ?? $request->input('tel');
        }

        if ($request->filled('whatsapp')) {
            $formatted = $this->formatPhoneNumber($request->input('whatsapp'));
            $user->whatsapp = $formatted ?? $request->input('whatsapp');
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profiles', 'public');
            // Optionally delete old photo
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $path;
        }

        // Handle password change if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
            if (property_exists($user, 'has_set_password')) {
                $user->has_set_password = true;
            }
        }

        $user->save();

        // Check if it's an AJAX request
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès !',
                'user' => $user,
            ]);
        }

        return redirect()->route('user.profile.edit')->with('success', 'Profil mis à jour.');
    }
}
