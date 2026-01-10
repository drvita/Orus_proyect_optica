<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenamePaymentsBancoDetails extends Migration
{
    public function up()
    {
        if (config('database.default') === 'sqlite') {
            // Para SQLite, creamos una nueva tabla
            Schema::create('config_new', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('value');
                $table->timestamps();
            });

            // Copiar solo los datos existentes
            DB::statement('INSERT INTO config_new (name, value) SELECT name, value FROM config');

            // Actualizar timestamps para los registros existentes
            DB::table('config_new')
                ->update([
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            // Eliminar tabla original
            Schema::drop('config');
            // Renombrar la nueva tabla
            Schema::rename('config_new', 'config');
        } else {
            // Para otros motores de DB, mantener la lógica original
            Schema::table('config', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('config', function (Blueprint $table) {
                $table->id()->first();
            });
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('banco', 'details');
            $table->foreignId('bank_id')
                ->after('total')
                ->nullable()
                ->constrained('config');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn('bank_id');
            $table->renameColumn('details', 'banco');
        });
    }
}
