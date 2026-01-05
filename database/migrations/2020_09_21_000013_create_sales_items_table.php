<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesItemsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sales_items';

    /**
     * Run the migrations.
     * @table ventas
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->integer('cant');
            $table->float('price');
            $table->float('subtotal');
            $table->integer('inStorage')->default(0);
            $table->integer('out')->nullable()->default(0);
            $table->string('session', 100);
            $table->string('descripcion', 200)->nullable();
            $table->foreignId('store_items_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
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
