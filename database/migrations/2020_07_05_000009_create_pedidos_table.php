<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePedidosTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'pedidos';

    /**
     * Run the migrations.
     * @table pedidos
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('test_id');
            $table->integer('id_cliente');
            $table->string('armazon_name', 250)->nullable();
            $table->string('armazon_code', 20)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('laboratorio', 200)->nullable();
            $table->string('npedidolab', 12)->nullable();
            $table->integer('ncaja')->nullable();
            $table->text('mensajes');
            $table->text('items');
            $table->integer('user_id');
            $table->integer('status');

            $table->index(["id_cliente"], 'contacto_id_idx');

            $table->index(["user_id"], 'user_id_idx');

            $table->index(["test_id"], 'test_id_idx');


            $table->foreign('id_cliente', 'contacto_id_idx')
                ->references('id')->on('contactos')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id', 'user_id_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('test_id', 'test_id_idx')
                ->references('id')->on('examenes')
                ->onDelete('no action')
                ->onUpdate('no action');
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
