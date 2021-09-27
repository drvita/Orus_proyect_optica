<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiledBranchesAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('atms', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')
                ->after('user_id')
                ->default(12);
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')
                ->after('user_id')
                ->default(12);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')
                ->after('user_id')
                ->default(12);
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')
                ->after('user_id')
                ->default(12);
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')
                ->after('user_id')
                ->default(12);
        });
        Schema::table('store_lots', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')
                ->after('user_id')
                ->default(12);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')
                ->after('remember_token')
                ->default(12);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('atms', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('store_items', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}