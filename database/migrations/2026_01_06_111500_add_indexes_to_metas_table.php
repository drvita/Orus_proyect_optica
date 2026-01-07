<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metas', function (Blueprint $table) {
            // Index for polymorphic lookup (id + type)
            // Compatible with MySQL and PostgreSQL
            $table->index(['metable_id', 'metable_type'], 'metas_metable_index');

            // Index for filtering by key
            $table->index('key', 'metas_key_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metas', function (Blueprint $table) {
            $table->dropIndex('metas_metable_index');
            $table->dropIndex('metas_key_index');
        });
    }
}
