<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sessions';

    /**
     * Run the migrations.
     * @table sessions
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('session_id', 40)->default('0');
            $table->string('ip_address', 45)->default('0');
            $table->string('user_agent', 120);
            $table->dateTime('last_activity');
            $table->text('user_data');
            $table->integer('user_id');

            $table->index(["user_id"], 'user_id_idx');

            $table->index(["last_activity"], 'last_activity_idx');


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
