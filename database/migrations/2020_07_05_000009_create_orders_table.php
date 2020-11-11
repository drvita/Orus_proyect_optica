<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'orders';

    /**
     * Run the migrations.
     * @table pedidos
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('session', 100);
            $table->text('observaciones')->nullable();
            $table->string('npedidolab', 12)->nullable();
            $table->integer('ncaja')->nullable();
            $table->integer('status')->default('0');
            //$table->text('mensajes')->nullable();
            $table->foreignId('lab_id')->nullable()->constrained('contacts');
            $table->foreignId('exam_id')->nullable()->constrained();
            $table->foreignId('contact_id')->constrained();
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
