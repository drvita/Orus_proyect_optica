<?php

namespace App\Observers;

use App\DTOs\Search\CreateNameRequest;
use App\DTOs\Search\UpdateNameRequest;
use App\Models\Contact;
use App\Services\SearchService;
use Illuminate\Support\Facades\Log;

class ContactObserver
{
    public function __construct(private SearchService $searchService) {}

    /**
     * Handle the Contact "created" event.
     */
    public function created(Contact $contact): void
    {
        try {
            if (!$contact->search_uuid) {
                $request = new CreateNameRequest(
                    name: $contact->name,
                    id: (string) $contact->id
                );

                $search = $this->searchService->createName($request);
                $contact->search_uuid = $search['id'];
                $contact->save();
            }
        } catch (\Exception $e) {
            Log::error('Error al sincronizar contacto con servicio de búsqueda', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function updated(Contact $contact)
    {
        $type = "";
        $dirty = $contact->getDirty();
        unset($dirty['updated_at']);
        unset($dirty['updated_id']);
        $data = ["user_id" => $contact->updated_id, "inputs" => $dirty];

        if (is_null($contact->deleted_at)) {
            $data['datetime'] = $contact->updated_at;
            $type = "updated";
        } else {
            $data['datetime'] = $contact->deleted_at;
            $type = "deleted";
        }

        $contact->metas()->create(["key" => $type, "value" => $data]);

        // Sync with search service if the name was changed and search_uuid exists
        try {
            if (isset($dirty['name']) && $contact->search_uuid) {
                $request = new UpdateNameRequest(
                    point_id: $contact->search_uuid,
                    name: $contact->name,
                    id: (string) $contact->id
                );

                $this->searchService->updateName($request);
            }
        } catch (\Exception $e) {
            Log::error('Error al actualizar contacto en servicio de búsqueda', [
                'contact_id' => $contact->id,
                'search_uuid' => $contact->search_uuid,
                'error' => $e->getMessage()
            ]);
        }
    }
}
