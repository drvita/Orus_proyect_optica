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
            $table->string('codebar', 100)->unique()->nullable();
            $table->string('grad', 12);
            $table->string('brand', 25);
            $table->string('name', 150)->unique();
            $table->string('unit', 4);
            $table->integer('cant');
            $table->float('price');
            $table->foreignId('category_id')->constrained();
            $table->foreignId('contact_id')->constrained();
            $table->foreignId('user_id')->constrained();
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
