<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->seed();
    }

    public function test_can_create_contact(): void
    {
        $user = User::factory()->create();
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'rfc' => 'XAXX010101000',
            'type' => 0,
            'phones' => [
                'cell' => '5555555555',
                'notices' => null,
                'office' => null
            ],
            'birthday' => '1990-01-01',
            'gender' => 'M', // Agregando campo requerido
            'domicilio' => [
                'street' => 'Test Street',
                'number_ext' => '123',
                'number_int' => 'A',
                'neighborhood' => 'Test Colony',
                'zip' => '12345',
                'location' => 'Test City',
                'state' => 'Test State'
            ],
            'business' => false
        ];

        $response = $this->actingAs($user)
            ->postJson(route('contacts.store'), $contactData);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'rfc',
                    'type',
                    'phones',
                    'metadata',
                    'address',
                    'business',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'rfc' => 'XAXX010101000',
            'type' => 0,
            'business' => false,
            'user_id' => $user->id
        ]);
    }
    public function test_contacts_index_route(): void
    {
        $user = User::factory()->create();
        $contacts = Contact::factory()->count(2)->create([
            'user_id' => $user->id
        ]);
        $contact = $contacts->first();

        $response = $this->actingAs($user)
            ->getJson(route('contacts.index', ["name" => substr($contact->name, 0, -3)]));

        $response->assertOk(); // This checks for 200 status code
    }
}
