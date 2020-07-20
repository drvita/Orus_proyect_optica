<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactosTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'contactos';

    /**
     * Run the migrations.
     * @table contactos
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('rfc', 15);
            $table->string('email', 100)->nullable();
            $table->integer('type');
            $table->text('telnumbers');
            $table->date('birthday')->nullable();
            $table->text('domicilio');
            $table->integer('user_id')->nullable();

            $table->index(["user_id"], 'id_user_idx');

            $table->unique(["rfc"], 'rfc_UNIQUE');


            $table->foreign('user_id', 'id_user_idx')
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
