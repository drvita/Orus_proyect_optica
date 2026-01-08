<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateData extends Command
{
    protected $signature = 'oru:migrate';
    protected $description = 'Migra datos de MySQL On-premise a PostgreSQL AWS';

    public function handle()
    {
        $this->info("Iniciando migración de Orus Óptica...");

        // 1. Obtener nombres de todas las tablas de MySQL
        $tables = DB::connection('mysql')->select('SHOW TABLES');
        $dbName = env('DB_DATABASE');
        $property = "Tables_in_{$dbName}";

        // 2. Desactivar constraints en Postgres para evitar errores de orden
        $this->warn("Desactivando triggers y FKs en Postgres...");
        DB::connection('pgsql_aws')->statement('SET session_replication_role = "replica";');

        foreach ($tables as $table) {
            $tableName = $table->$property;
            $this->info("Copiando tabla: {$tableName}...");

            // 3. Limpiar tabla destino antes de insertar (Opcional, pero recomendado)
            DB::connection('pgsql_aws')->statement("TRUNCATE TABLE {$tableName} RESTART IDENTITY CASCADE;");

            // 4. Procesar por bloques para no saturar memoria
            $count = 0;
            $chunkSize = ($tableName === 'exams') ? 400 : 1000;
            DB::connection('mysql')->table($tableName)->orderByRaw('1')->chunk($chunkSize, function ($rows) use ($tableName, &$count) {
                $data = array_map(function ($row) {
                    return (array) $row;
                }, $rows->toArray());

                DB::connection('pgsql_aws')->table($tableName)->insert($data);
                $count += count($data);
                $this->output->write("."); // Progreso visual
            });

            $this->info("\n[OK] {$tableName}: {$count} registros movidos.");
        }

        // 5. Reactivar constraints
        DB::connection('pgsql_aws')->statement('SET session_replication_role = "origin";');

        $this->info("--------------------------------------------------");
        $this->info("Migración finalizada con éxito, Ingeniero.");
        $this->call('orus:fix-sequences'); // Llamada al fix de secuencias
    }
}
