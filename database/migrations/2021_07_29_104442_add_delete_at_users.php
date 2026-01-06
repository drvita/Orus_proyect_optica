<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeleteAtUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('deleted_at')
                ->after('remember_token')
                ->nullable();

            // For PostgreSQL compat with soft deletes
            $table->dropUnique(['username']);
            $table->dropUnique(['email']);
            $table->dropUnique(['api_token']);

            $table->unique('username')->whereNull('deleted_at');
            $table->unique('email')->whereNull('deleted_at');
            $table->unique('api_token')->whereNull('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}
