<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTablesAddDeleteAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_id')
                ->after('user_id')
                ->nullable();
            $table->dateTime('deleted_at')
                ->after('updated_at')
                ->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_id')
                ->after('user_id')
                ->nullable();
            $table->dateTime('deleted_at')
                ->after('updated_at')
                ->nullable();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_id')
                ->after('user_id')
                ->nullable();
            $table->dateTime('deleted_at')
                ->after('updated_at')
                ->nullable();
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_id')
                ->after('user_id')
                ->nullable();
            $table->dateTime('deleted_at')
                ->after('updated_at')
                ->nullable();

            // For PostgreSQL compat with soft deletes
            $table->dropUnique(['session']);
            $table->unique('session')->whereNull('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropColumn('updated_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropColumn('updated_id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropColumn('updated_id');
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropColumn('updated_id');
        });
    }
}
