<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class FirstLoginController extends Controller
{
    public function showForm($token)
    {
        $user = User::where('first_login_token', $token)->firstOrFail();
        return view('auth.first-login', compact('user'));
    }

    public function setPassword(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::where('first_login_token', $token)->firstOrFail();

        $user->update([
            'password' => Hash::make($request->password),
            'first_login_token' => null,
            'has_set_password' => true,
        ]);

        Auth::login($user, true);

        return redirect()->route('app_accueil')->with('success', 'Mot de passe défini avec succès !');
    }
}
