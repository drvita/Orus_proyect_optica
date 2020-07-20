<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVentasTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'ventas';

    /**
     * Run the migrations.
     * @table ventas
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('session', 120);
            $table->integer('cliente_id');
            $table->integer('pedido_id')->nullable();
            $table->text('items');
            $table->string('metodopago', 30)->default('EFECTIVO');
            $table->float('subtotal');
            $table->float('descuento')->nullable();
            $table->float('anticipo')->nullable();
            $table->float('total');
            $table->text('banco')->nullable();
            $table->integer('user_id');

            $table->index(["pedido_id"], 'pedido_id_idx');

            $table->index(["cliente_id"], 'contacto_id_idx');

            $table->index(["user_id"], 'user_id_idx');

            $table->unique(["session"], 'session_UNIQUE');


            $table->foreign('cliente_id', 'contacto_id_idx')
                ->references('id')->on('contactos')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('pedido_id', 'pedido_id_idx')
                ->references('id')->on('pedidos')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id', 'user_id_idx')
                ->references('id')->on('users')
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
