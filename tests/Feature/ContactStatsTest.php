<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactStatsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // $this->withoutExceptionHandling();
        $this->seed();
    }

    public function test_can_get_contact_stats(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $user->id
        ]);

        // Simular algunas relaciones si es necesario para el factory, 
        // pero por defecto vendran en 0, lo cual es valido para el test.

        $response = $this->actingAs($user)
            ->getJson("/api/contacts/{$contact->id}/stats");

        $response->assertOk();

        dd($response->json());
    }
}
