<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateData extends Command
{
    protected $signature = 'orus:migrate';
    protected $description = 'Migra datos de MySQL On-premise a PostgreSQL AWS';

    public function handle()
    {
        $this->info("Iniciando migración de Orus Óptica...");

        // 1. Obtener nombres de todas las tablas de MySQL
        $tables = DB::connection('mysql')->select('SHOW TABLES');

        // 2. Desactivar constraints en Postgres
        $this->warn("Desactivando triggers y FKs en Postgres...");
        DB::connection('pgsql_aws')->statement('SET session_replication_role = "replica";');

        foreach ($tables as $table) {
            // Obtenemos el nombre de la tabla de forma segura (primer valor del objeto)
            $tableArray = (array) $table;
            $tableName = reset($tableArray);

            if (!$tableName) {
                $this->error("No se pudo determinar el nombre de una tabla. Saltando...");
                continue;
            }

            $this->info("--------------------------------------------------");
            $this->info("Procesando tabla: {$tableName}");

            // Contar registros en origen
            $sourceCount = DB::connection('mysql')->table($tableName)->count();
            $this->info("Registros en MySQL: {$sourceCount}");

            if ($sourceCount == 0) {
                $this->warn("La tabla {$tableName} está vacía en origen. Saltando...");
                continue;
            }

            // 3. Limpiar tabla destino
            try {
                DB::connection('pgsql_aws')->statement("TRUNCATE TABLE {$tableName} RESTART IDENTITY CASCADE;");
            } catch (\Throwable $e) {
                $this->error("Error al limpiar tabla {$tableName}: " . $e->getMessage());
                continue;
            }

            // 4. Migrar datos por bloques
            $insertedCount = 0;
            $chunkSize = ($tableName === 'exams') ? 400 : 1000;

            try {
                DB::connection('mysql')->table($tableName)->orderByRaw('1')->chunk($chunkSize, function ($rows) use ($tableName, &$insertedCount) {
                    $data = array_map(function ($row) {
                        return (array) $row;
                    }, $rows->toArray());

                    DB::connection('pgsql_aws')->table($tableName)->insert($data);
                    $insertedCount += count($data);
                    $this->output->write("."); // Progreso visual
                });
            } catch (\Throwable $e) {
                $this->error("\nError al insertar en {$tableName}: " . $e->getMessage());
                // No continuamos con la validación si falló la inserción
                continue;
            }

            $this->info("\n");

            // 5. Validación final
            $destCount = DB::connection('pgsql_aws')->table($tableName)->count();
            $this->info("Registros migrados: {$insertedCount}");
            $this->info("Total en PostgreSQL: {$destCount}");

            if ($sourceCount === $destCount) {
                $this->info("[ÉXITO] La migración de {$tableName} fue correcta.");
            } else {
                $this->error("[ALERTA] Discrepancia en {$tableName}: Origen={$sourceCount}, Destino={$destCount}");
            }
        }

        // 6. Reactivar constraints
        DB::connection('pgsql_aws')->statement('SET session_replication_role = "origin";');

        $this->info("--------------------------------------------------");
        $this->info("Migración finalizada.");
        $this->call('orus:fix-sequences');
    }
}
