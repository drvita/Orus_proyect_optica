<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreLotTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'store_lot';

    /**
     * Run the migrations.
     * @table store_lot
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('bill', 20)->default('--');
            $table->text('base64');
            $table->decimal('cost', 10, 2);
            $table->decimal('price', 10, 2);
            $table->integer('amount');
            $table->foreignId('store_items_id')->constrained();
            /*
            $table->index(["item_id"], 'item_id_idx');

            $table->foreign('item_id', 'item_id_idx')
                ->references('id')->on('store_items')
                ->onDelete('no action')
                ->onUpdate('no action');
            */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
