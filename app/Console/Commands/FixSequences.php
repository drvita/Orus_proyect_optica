<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSequences extends Command
{
    protected $signature = 'orus:fix-sequences';
    protected $description = 'Corrige secuencias de PostgreSQL';

    public function handle()
    {
        $this->info("Sincronizando secuencias en PostgreSQL...");

        $tables = DB::connection('pgsql_aws')->select("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public'
    ");

        // Tablas a excluir explícitamente porque no usan ID autoincremental entero
        $excludedTables = [
            'notifications',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
            'password_resets',
            'failed_jobs'
        ];

        foreach ($tables as $table) {
            $name = $table->table_name;

            if (in_array($name, $excludedTables)) {
                $this->comment("Saltando tabla excluida: {$name}");
                continue;
            }

            // Intentar resetear la secuencia de la columna 'id'
            try {
                DB::connection('pgsql_aws')->statement("
                SELECT setval(pg_get_serial_sequence('{$name}', 'id'), 
                COALESCE(MAX(id), 1)) FROM {$name}
            ");
            } catch (\Exception $e) {
                // Algunas tablas podrían no tener columna 'id' o secuencia, silenciamos el error en consola
                // pero ya no "ensuciará" el log de Postgres con errores obvios.
                continue;
            }
        }
        $this->info("Secuencias sincronizadas.");
    }
}
