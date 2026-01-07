<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSequences extends Command
{
    protected $signature = 'migrate:fix-sequences';
    protected $description = 'Corrige secuencias de PostgreSQL';

    public function handle()
    {
        $this->info("Sincronizando secuencias en PostgreSQL...");

        $tables = DB::connection('pgsql_aws')->select("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public'
    ");

        foreach ($tables as $table) {
            $name = $table->table_name;
            // Intentar resetear la secuencia de la columna 'id'
            try {
                DB::connection('pgsql_aws')->statement("
                SELECT setval(pg_get_serial_sequence('{$name}', 'id'), 
                COALESCE(MAX(id), 1)) FROM {$name}
            ");
            } catch (\Exception $e) {
                // Algunas tablas podrían no tener columna 'id' o secuencia
                continue;
            }
        }
        $this->info("Secuencias sincronizadas.");
    }
}
