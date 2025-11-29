<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Informations fournies
        $prenom = 'Boubacar';
        $nom = 'Kahn Diallo';
        $email = 'boubacarkahndiallo@gmail.com';
        $telephone = '621554784';
        $plainPassword = '4784';

        // Create or update the super admin user
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'prenom' => $prenom,
                'nom' => $nom,
                'email' => $email,
                'tel' => $telephone,
                'telephone' => $telephone,
                'role' => 'super_admin',
                'has_set_password' => true,
                'password' => Hash::make($plainPassword),
                'email_verified_at' => now(),
            ]
        );

        if ($user->wasRecentlyCreated) {
            $this->command && $this->command->info('Super admin créé: ' . $email);
        } else {
            $this->command && $this->command->info('Super admin mis à jour: ' . $email);
        }
    }
}
