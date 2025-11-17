<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure l'environnement WhatsApp pour les tests
        config(['services.whatsapp' => [
            'url' => 'https://graph.facebook.com/v17.0',
            'phone_number_id' => '621554784', // Utilise le numéro fixe
            'token' => 'test-token'
        ]]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_it_creates_user_and_sends_whatsapp_password()
    {
        // Mock l'API WhatsApp
        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        // Données de test
        $userData = [
            'nom' => 'Doe',
            'prenom' => 'John',
            'tel' => '+33612345678',
            'whatsapp' => '+33612345678',
            'email' => 'john.doe@example.com',
            'role' => 'user',
            'adresse' => '123 Test Street'
        ];

        // Crée l'utilisateur via l'API
        $response = $this->postJson('/api/utilisateurs', $userData);

        // Vérifie la réponse
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Utilisateur créé avec succès'
            ]);

        // Vérifie que l'utilisateur existe en BDD
        $this->assertDatabaseHas('users', [
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john.doe@example.com'
        ]);

        // Vérifie que le message WhatsApp a été envoyé
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '621554784/messages') &&
                   str_contains($request->data()['text']['body'], 'Votre mot de passe');
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_it_creates_user_without_whatsapp()
    {
        // Données de test sans WhatsApp
        $userData = [
            'nom' => 'Doe',
            'prenom' => 'Jane',
            'tel' => '+33612345679',
            'email' => 'jane.doe@example.com',
            'role' => 'user',
            'adresse' => '456 Test Avenue'
        ];

        // Crée l'utilisateur
        $response = $this->postJson('/api/utilisateurs', $userData);

        // Vérifie la réponse
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Utilisateur créé avec succès'
            ]);

        // Vérifie que l'utilisateur existe
        $this->assertDatabaseHas('users', [
            'nom' => 'Doe',
            'prenom' => 'Jane',
            'email' => 'jane.doe@example.com'
        ]);

        // Vérifie qu'aucun appel WhatsApp n'a été fait
        Http::assertNothingSent();
    }
}