<?php

namespace App\Observers;

use App\Models\StoreBranch;
use Illuminate\Support\Facades\Log;

class StoreBranchObserver
{
    /**
     * Handle the StoreBranch "created" event.
     */
    public function created(StoreBranch $storeBranch): void
    {
        Log::info("[StoreBranchObserver] Branch created: " . $storeBranch->id);
    }

    /**
     * Handle the StoreBranch "updated" event.
     */
    public function updated(StoreBranch $storeBranch): void
    {
        Log::info("[StoreBranchObserver] Branch updated: " . $storeBranch->id);
    }

    /**
     * Handle the StoreBranch "deleted" event.
     */
    public function deleted(StoreBranch $storeBranch): void
    {
        Log::info("[StoreBranchObserver] Branch deleted: " . $storeBranch->id);
    }
}
