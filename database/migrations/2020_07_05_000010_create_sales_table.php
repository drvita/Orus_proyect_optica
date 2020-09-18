<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sales';

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
            $table->text('items');
            $table->string('metodopago', 30)->default('EFECTIVO');
            $table->float('subtotal');
            $table->float('descuento')->nullable();
            $table->float('anticipo')->nullable();
            $table->float('total');
            $table->text('banco')->nullable();
            $table->foreignId('contact_id')->constrained();
            $table->foreignId('order_id')->nullable()->constrained();
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
