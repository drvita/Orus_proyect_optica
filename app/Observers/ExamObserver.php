<?php

namespace App\Observers;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExamObserver
{
    /**
     * Handle the Exam "creating" event.
     */
    public function creating(Exam $exam): void
    {
        $exam->status = 0;
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $exam->user_id = $user->id;
            $exam->branch_id = $user->branch_id;

            if (!$user->hasRole("doctor")) {
                $doctor_assigned = User::where("branch_id", $user->branch_id)
                    ->role("doctor")
                    ->first();
                if ($doctor_assigned) {
                    $exam->user_id = $doctor_assigned->id;
                }

                $graduationFields = [
                    'esferaoi',
                    'esferaod',
                    'cilindroi',
                    'cilindrod',
                    'ejeoi',
                    'ejeod',
                    'adicioni',
                    'adiciond',
                    'dpoi',
                    'dpod',
                    'avfod',
                    'avfoi',
                    'avf2o'
                ];

                foreach ($graduationFields as $field) {
                    if ($exam->$field !== null && $exam->$field != 0 && $exam->$field !== '') {
                        $exam->status = 1;
                        break;
                    }
                }
            }
        }
    }

    /**
     * Handle the Exam "updating" event.
     */
    public function updating(Exam $exam): void
    {
        Log::info("[ExamObserver] exam updating working: ", [
            "exam_id" => $exam->id,
            "is_auth" => Auth::check(),
            "exam_status" => $exam->status,
        ]);
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $exam->updated_id = $user->id;

            if ($user->hasRole("doctor")) {
                if ($exam->category_id && $exam->status == 0) {
                    Log::info("[ExamObserver] exam end status: " . $exam->id);
                    $exam->status = 1;
                }

                if ($exam->isDirty('status') && $exam->status == 1 && !$exam->ended_at) {
                    Log::info("[ExamObserver] exam end time: " . $exam->id);
                    $exam->ended_at = now();
                    $exam->user_id = $user->id;
                }
            }
        }
    }
}
