<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExamTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_exam()
    {
        $this->withoutExceptionHandling();
        $user = User::role('admin')->inRandomOrder()->first();
        $contact = Contact::inRandomOrder()->first();
        $this->actingAs($user);

        $res = $this->json('POST', 'api/exams', [
            "contact_id" => $contact->id,
        ]);
        //dd($res->decodeResponseJson());
        $res->assertStatus(201);
    }
}
