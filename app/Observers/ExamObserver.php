<?php

namespace App\Observers;

use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExamObserver
{
    /**
     * Handle the Exam "creating" event.
     */
    public function creating(Exam $exam): void
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $exam->user_id = $user->id;
            $exam->branch_id = $user->branch_id;
        }
        $exam->status = 0;
    }

    /**
     * Handle the Exam "updating" event.
     */
    public function updating(Exam $exam): void
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $exam->updated_id = $user->id;

            if ($user->hasRole("doctor") && $exam->category_id && $exam->status == 0) {
                Log::info("[ExamObserver] exam end status: " . $exam->id);
                $exam->status = 1;
            }

            if ($exam->isDirty('status') && $exam->status == 1 && !$exam->ended_at) {
                Log::info("[ExamObserver] exam end time: " . $exam->id);
                $exam->ended_at = now();
            }
        }
    }

    /**
     * Handle the Exam "updated" event.
     */
    public function updated(Exam $exam): void
    {
        $type = "";
        $changes = $exam->getChanges();
        unset($changes['updated_at']);
        unset($changes['updated_id']);

        if (count($changes) > 0) {
            $data = ["user_id" => $exam->updated_id, "inputs" => $changes];

            if (is_null($exam->deleted_at)) {
                $data['datetime'] = $exam->updated_at;
                $type = "updated";
            } else {
                $data['datetime'] = $exam->deleted_at;
                $type = "deleted";
            }

            $exam->metas()->create(["key" => $type, "value" => $data]);
        }
    }
}
