<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreItemsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'store_items';

    /**
     * Run the migrations.
     * @table store_items
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('code', 18)->unique();
            $table->string('codebar', 100)->nullable()->unique();
            $table->string('grad', 12)->nullable();
            $table->string('brand', 25)->nullable();
            $table->string('name', 150)->unique();
            $table->string('unit', 4);
            $table->integer('cant')->nullable();
            $table->float('price')->nullable();
            $table->foreignId('category_id')->constrained();
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
