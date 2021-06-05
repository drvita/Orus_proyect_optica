<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePaymentsBancoDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('config', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        Schema::table('config', function (Blueprint $table) {
            $table->id()->first();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('banco', 'details');
            $table->foreignId('bank_id')
                ->after('total')
                ->nullable()
                ->constrained('config');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('details');
            $table->dropColumn('bank_id');
        });
    }
}
