<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->seed();
    }

    public function test_create_exam()
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create();

        $this->actingAs($user)
            ->postJson(route('exams.store'), [
                "contact_id" => $contact->id,
            ])
            ->assertStatus(200);
    }
}
