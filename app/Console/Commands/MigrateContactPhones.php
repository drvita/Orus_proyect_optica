<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;
use Exception;
use Illuminate\Support\Facades\DB;

class MigrateContactPhones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:contacts-migrate-phones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate phone numbers from contacts telnumbers to phone_numbers table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $total = Contact::count();
        $this->info("Starting migration for {$total} contacts...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        try {
            Contact::chunk(100, function ($contacts) use ($bar) {
                foreach ($contacts as $contact) {
                    $telnumbers = $contact->telnumbers;

                    if (is_array($telnumbers)) {
                        foreach ($telnumbers as $key => $number) {
                            if (empty($number)) {
                                continue;
                            }

                            // Clean type key
                            $type = str_replace('t_', '', strtolower($key));

                            // Normalize cell/mobil
                            if ($type === 'cell' || $type === 'mobil') {
                                $type = 'movil';
                            }

                            $contact->phones()->create([
                                'type' => $type,
                                'number' => $number,
                                'country_code' => '+52' // Default as requested
                            ]);
                        }
                    }
                    $bar->advance();
                }
            });
        } catch (Exception $e) {
            $this->error("\nError during migration: " . $e->getMessage());
            return Command::FAILURE;
        }

        $bar->finish();
        $this->info("\nMigration completed successfully.");

        return Command::SUCCESS;
    }
}
