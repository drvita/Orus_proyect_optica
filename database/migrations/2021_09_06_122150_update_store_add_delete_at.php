<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStoreAddDeleteAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_items', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_id')
                ->after('user_id')
                ->nullable();
            $table->dateTime('deleted_at')
                ->after('updated_at')
                ->nullable();

            // For PostgreSQL compat with soft deletes
            $table->dropUnique(['code']);
            $table->dropUnique(['codebar']);
            $table->dropUnique(['name']);

            $table->unique('code')->whereNull('deleted_at');
            $table->unique('codebar')->whereNull('deleted_at');
            $table->unique('name')->whereNull('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_items', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropColumn('updated_id');
        });
    }
}
