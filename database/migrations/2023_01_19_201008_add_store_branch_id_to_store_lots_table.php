<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreBranchIdToStoreLotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('store_lots', 'store_branch_id')) {
            // Drop bill column
            Schema::table('store_lots', function (Blueprint $table) {
                $table->dropColumn('bill');
            });

            // Drop base64 column
            Schema::table('store_lots', function (Blueprint $table) {
                $table->dropColumn('base64');
            });

            // Drop amount column
            Schema::table('store_lots', function (Blueprint $table) {
                $table->dropColumn('amount');
            });

            // Drop branch_id column
            Schema::table('store_lots', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });

            // Add new columns
            Schema::table('store_lots', function (Blueprint $table) {
                $table->decimal('cant', 10, 2)->after('price');
                $table->string('num_invoice', 100)->after('cant');
                $table->foreignId('store_branch_id')
                    ->after('num_invoice')
                    ->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('store_lots', 'store_branch_id')) {
            // Remove new columns
            Schema::table('store_lots', function (Blueprint $table) {
                $table->dropColumn('store_branch_id');
            });

            Schema::table('store_lots', function (Blueprint $table) {
                $table->dropColumn('num_invoice');
            });

            Schema::table('store_lots', function (Blueprint $table) {
                $table->dropColumn('cant');
            });

            // Restore original columns
            Schema::table('store_lots', function (Blueprint $table) {
                $table->string('bill')->nullable();
                $table->text('base64')->nullable();
                $table->decimal('amount', 10, 2)->nullable();
                $table->foreignId('branch_id')->nullable();
            });
        }
    }
}
