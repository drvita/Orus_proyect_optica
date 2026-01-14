<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'contacts';

    /**
     * Run the migrations.
     * @table contactos
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rfc', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->integer('type');
            $table->integer('business');
            $table->jsonb('telnumbers');
            $table->date('birthday')->nullable();
            $table->text('domicilio')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            /*
            $table->foreign('user_id', 'id_user_idx')
                ->references('id')->on('users')
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
