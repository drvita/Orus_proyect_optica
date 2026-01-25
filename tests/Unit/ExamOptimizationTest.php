<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\User;
use App\Models\Contact;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamOptimizationTest extends TestCase
{
    public function test_exam_lifecycle_optimization()
    {
        DB::beginTransaction();

        try {
            // Setup
            $user = User::first(); // Use existing user or factory
            if (!$user) {
                $user = User::factory()->create();
            }
            $this->actingAs($user);

            $contact = Contact::first();
            if (!$contact) {
                $contact = new Contact();
                $contact->save();
            }

            // 1. Create Exam
            $exam = new Exam();
            $exam->contact_id = $contact->id;
            $exam->user_id = $user->id;
            $exam->save();

            // Verify Status 0
            $this->assertEquals(0, $exam->status, "Exam should start with status 0");

            // 2. Test StartTime
            // Manually call startTime as it might be called by Controller/Frontend
            $exam->startTime();
            $this->assertTrue($exam->metas()->where('key', 'start_exam')->exists(), 'Start exam meta should exist');

            // Call again to ensure no duplicate
            $initialCount = $exam->metas()->where('key', 'start_exam')->count();
            $exam->startTime();
            $this->assertEquals($initialCount, $exam->metas()->where('key', 'start_exam')->count(), 'Start exam meta should not duplicate');

            // 3. Test Observer Update
            // Make a unique change
            $uniqueVal = rand(10, 90);
            $exam->edad = $uniqueVal;
            $exam->save();

            // Check if meta created
            // Observer creates 'updated' key
            $updatedMeta = $exam->metas()->where('key', 'updated')->latest()->first();
            $this->assertNotNull($updatedMeta, "Updated meta should be created on change");

            $inputs = $updatedMeta->value['inputs'] ?? [];
            $this->assertArrayHasKey('edad', $inputs, "Updated meta should capture changed field");

            // 4. Test Update without change
            $countBefore = $exam->metas()->count();
            $exam->touch(); // Just updates updated_at, but observer filters it
            $exam->save();
            // Should verify no NEW meta
            $countAfter = $exam->metas()->count();
            // Note: touch() updates updated_at. Our observer filters updated_at.
            // If getChanges only contains updated_at, count($changes) > 0 check handles it?
            // "unset($changes['updated_at'])" -> changes might be empty.
            $this->assertEquals($countBefore, $countAfter, "No meta should be created for timestamp-only updates");

            // 5. Test EndTime (Status Change)
            // Need a category_id for the checks
            $exam->category_id = 1;
            $exam->status = 1;
            $exam->save();

            $this->assertTrue($exam->metas()->where('key', 'end_exam')->exists(), 'End exam meta should be created when status becomes 1');

            // 6. Test EndTime no duplicate on subsequent save
            $endCountBefore = $exam->metas()->where('key', 'end_exam')->count();
            $exam->observaciones = "Test";
            $exam->save();
            $endCountAfter = $exam->metas()->where('key', 'end_exam')->count();

            $this->assertEquals($endCountBefore, $endCountAfter, "End exam meta should not be duplicated on subsequent updates");

            // 7. Verify Duration Calculation
            // We need to manipulate the metas to ensure meaningful duration
            $startMeta = $exam->metas()->where('key', 'start_exam')->first();
            $endMeta = $exam->metas()->where('key', 'end_exam')->first();

            // Hack values
            $startTime = now()->subMinutes(60);
            $endTime = now();

            $startMeta->update(['value' => [
                'datetime' => $startTime->toDateTimeString(),
                'user_id' => $user->id
            ]]);
            $endMeta->update(['value' => [
                'datetime' => $endTime->toDateTimeString(),
                'user_id' => $user->id
            ]]);

            // Reload exam relations
            $exam->refresh();
            $exam->load('metas');

            // Check duration
            $duration = $exam->duration;
            $this->assertEquals(60, $duration, "Duration should be calculated correctly in minutes");

            echo "\nOptimization Test Passed Successfully!\n";
        } catch (\Exception $e) {
            echo "\nTest Failed: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            DB::rollBack();
        }
    }
}
