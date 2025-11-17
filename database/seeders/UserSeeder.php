<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'nom' => 'Super',
                'prenom' => 'Admin',
                'email' => 'superadmin@example.com',
                'tel' => '622000001',
                'role' => 'super_admin',
                'password' => Hash::make('SuperAdmin123!'),
                'has_set_password' => true,
                'is_verified' => true,
                'actifs' => 1,
            ],
            [
                'nom' => 'Site',
                'prenom' => 'Admin',
                'email' => 'admin@example.com',
                'tel' => '622000002',
                'role' => 'admin',
                'password' => Hash::make('Admin123!'),
                'has_set_password' => true,
                'is_verified' => true,
                'actifs' => 1,
            ],
            [
                'nom' => 'Client',
                'prenom' => 'Test',
                'email' => 'user@example.com',
                'tel' => '622000003',
                'role' => 'user',
                'password' => Hash::make('User123!'),
                'has_set_password' => true,
                'is_verified' => true,
                'actifs' => 1,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
