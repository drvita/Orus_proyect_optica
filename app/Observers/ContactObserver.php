<?php

namespace App\Observers;

use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ContactObserver
{
    /**
     * handle the Contact "creating" event.
     */
    public function creating(Contact $contact): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $contact->user_id = $user->id;
        }
    }
    /**
     * Handle the Contact "created" event.
     */
    public function created(Contact $contact): void
    {
        $this->syncPhones($contact);
    }

    /**
     * Handle the Contact "updating" event.
     */
    public function updating(Contact $contact): void
    {
        if (Auth::check()) {
            $contact->updated_id = Auth::id();
        }
    }

    /**
     * Handle the Contact "updated" event.
     */
    public function updated(Contact $contact): void
    {
        $this->syncPhones($contact);
    }

    /**
     * Synchronize phone numbers from the telnumbers array to the phone_numbers table.
     */
    private function syncPhones(Contact $contact): void
    {
        $telnumbers = $contact->telnumbers;

        if (!is_array($telnumbers)) {
            return;
        }

        $normalizedData = [];
        foreach ($telnumbers as $key => $number) {
            if (empty($number)) {
                continue;
            }

            // Clean type key: remove t_, lowercase
            $type = str_replace('t_', '', strtolower($key));

            // Normalize cell/mobil to movil
            if ($type === 'cell' || $type === 'mobil') {
                $type = 'movil';
            }

            $normalizedData[$type] = $number;
        }

        // Get existing phone numbers for this contact
        $existingPhones = $contact->phones()->get();

        foreach ($normalizedData as $type => $number) {
            $phone = $existingPhones->where('type', $type)->first();

            if ($phone) {
                // Update if number changed
                if ($phone->number !== $number) {
                    $phone->update(['number' => $number]);
                }
            } else {
                // Create new
                $contact->phones()->create([
                    'type' => $type,
                    'number' => $number,
                    'country_code' => '+52'
                ]);
            }
        }

        // Delete ones that are no longer in telnumbers
        foreach ($existingPhones as $phone) {
            if (!isset($normalizedData[$phone->type])) {
                $phone->delete();
            }
        }
    }
}
