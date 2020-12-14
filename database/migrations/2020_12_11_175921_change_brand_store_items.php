<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBrandStoreItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // make sure the index is dropped first
        Schema::table('store_items', function (Blueprint $table) {
            $table->dropColumn('brand');
        });
        // now change the type of the field
        Schema::table('store_items', function (Blueprint $table) {
            $table->foreignId('brand_id')->after('price')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
