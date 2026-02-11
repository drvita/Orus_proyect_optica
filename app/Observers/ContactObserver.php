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

    private function syncPhones(Contact $contact): void
    {
        $telnumbers = $contact->telnumbers;

        // If not an array or null, delete existing phones and return
        if (!is_array($telnumbers)) {
            $contact->phones()->delete();
            return;
        }

        $normalizedData = [];
        foreach ($telnumbers as $key => $number) {
            $number = is_string($number) ? trim($number) : $number;
            if (empty($number)) {
                continue;
            }

            // Normalizar llave (e.g., "t_movil" -> "movil")
            $type = str_replace('t_', '', strtolower(trim($key)));

            // Normalización específica de tipos conocidos
            if ($type === 'cell' || $type === 'mobil') {
                $type = 'movil';
            }

            // El último número para un mismo tipo gana (evita duplicados en el array)
            $normalizedData[$type] = $number;
        }

        // Estrategia: Eliminar todo y re-crear
        $contact->phones()->delete();

        foreach ($normalizedData as $type => $number) {
            $contact->phones()->create([
                'type' => $type,
                'number' => $number,
                'country_code' => '+52'
            ]);
        }
    }
}
