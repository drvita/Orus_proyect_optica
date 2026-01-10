<?php

namespace App\Console\Commands;

use App\DTOs\Search\CreateNameRequest;
use App\Models\Contact;
use App\Services\SearchService;
use Illuminate\Console\Command;

class SyncContactsToSearchService extends Command
{
    protected $signature = 'orus:sync-contacts-qdrant';
    protected $description = 'Sincroniza todos los contactos con el servicio de búsqueda';

    public function __construct(private SearchService $searchService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $contacts = Contact::select('id', 'name')->whereNull("search_uuid")->cursor();
        $bar = $this->output->createProgressBar(Contact::count());
        $bar->start();

        $success = 0;
        $errors = 0;

        foreach ($contacts as $contact) {
            try {
                $request = new CreateNameRequest(
                    name: $contact->name,
                    id: (string) $contact->id
                );

                $search = $this->searchService->createName($request);
                $contact->search_uuid = $search['id'];
                $contact->save();
                $success++;
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error al procesar contacto {$contact->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Sincronización completada:");
        $this->info("- Contactos procesados exitosamente: {$success}");
        $this->info("- Errores encontrados: {$errors}");

        return Command::SUCCESS;
    }
}
