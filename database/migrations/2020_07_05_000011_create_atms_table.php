<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtmsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'atms';

    /**
     * Run the migrations.
     * @table caja
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->float('efectivo')->nullable();
            $table->float('tarjetas')->nullable();
            $table->float('cheques')->nullable();
            $table->float('venta')->nullable();
            $table->foreignId('session_id', 120)->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            /*
            $table->index(["session_id"], 'ventas_session_idx');
            $table->index(["user_id"], 'user_id_idx');

            $table->foreign('user_id', 'user_id_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');
            $table->foreign('session_id', 'ventas_session_idx')
                ->references('session')->on('ventas')
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
