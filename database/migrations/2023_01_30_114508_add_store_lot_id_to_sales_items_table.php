<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreLotIdToSalesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_items', function (Blueprint $table) {
            $table->foreignId('store_branch_id')
                ->after('store_items_id')
                ->nullable();
            $table->foreignId('store_lot_id')
                ->after('store_branch_id')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_items', function (Blueprint $table) {
            $table->dropColumn('store_lot_id');
            $table->dropColumn('store_branch_id');
        });
    }
}
