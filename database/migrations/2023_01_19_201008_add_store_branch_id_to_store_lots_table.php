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
            Schema::table('store_lots', function (Blueprint $table) {
                $table->dropColumn('bill');
                $table->dropColumn('base64');
                $table->dropColumn('amount');
                $table->dropColumn('branch_id');
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
            Schema::table('store_lots', function (Blueprint $table) {
                $table->dropColumn('store_branch_id');
            });
        }
    }
}
