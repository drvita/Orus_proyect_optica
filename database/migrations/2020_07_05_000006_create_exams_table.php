<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'exams';

    /**
     * Run the migrations.
     * @table examenes
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->integer('edad')->default('0');
            $table->string('keratometriaoi', 13)->nullable();
            $table->string('keratometriaod', 13)->nullable();
            $table->string('pantalleooi', 13)->nullable();
            $table->string('pantalleood', 13)->nullable();
            $table->text('interrogatorio')->nullable();
            $table->tinyInteger('cefalea')->nullable();
            $table->string('c_frecuencia', 60)->nullable();
            $table->tinyInteger('c_intensidad')->nullable();
            $table->tinyInteger('frontal')->nullable();
            $table->tinyInteger('temporal')->nullable();
            $table->tinyInteger('occipital')->nullable();
            $table->tinyInteger('generality')->nullable();
            $table->tinyInteger('temporaoi')->nullable();
            $table->tinyInteger('temporaod')->nullable();
            $table->text('coa')->nullable();
            $table->text('aopp')->nullable();
            $table->text('aopf')->nullable();
            $table->string('avsloi', 13)->nullable();
            $table->string('avslod', 13)->nullable();
            $table->string('avcgaoi', 13)->nullable();
            $table->string('avcgaod', 13)->nullable();
            $table->string('cvoi', 13)->nullable();
            $table->string('cvod', 13)->nullable();
            $table->text('oftalmoscopia')->nullable();
            $table->string('rsoi', 17)->nullable();
            $table->string('rsod', 17)->nullable();
            $table->string('diagnostico', 30)->nullable();
            $table->tinyInteger('presbicie')->nullable();
            $table->text('txoftalmico')->nullable();
            $table->float('esferaoi')->nullable();
            $table->float('esferaod')->nullable();
            $table->float('cilindroi')->nullable();
            $table->float('cilindrod')->nullable();
            $table->integer('ejeoi')->nullable();
            $table->integer('ejeod')->nullable();
            $table->float('adicioni')->nullable();
            $table->float('adiciond')->nullable();
            $table->float('dpoi')->nullable();
            $table->float('dpod')->nullable();
            $table->string('avfod', 13)->nullable();
            $table->string('avfoi', 13)->nullable();
            $table->string('avf2o', 25)->nullable();
            $table->string('lcmarca', 70)->nullable();
            $table->string('lcgoi', 30)->nullable();
            $table->string('lcgod', 30)->nullable();
            $table->text('txoptico')->nullable();
            $table->float('alturaod')->nullable();
            $table->float('alturaoi')->nullable();
            $table->integer('pioi')->nullable();
            $table->integer('piod')->nullable();
            $table->text('observaciones')->nullable();
            $table->tinyInteger('pc')->nullable();
            $table->tinyInteger('tablet')->nullable();
            $table->tinyInteger('movil')->nullable();
            $table->tinyInteger('lap')->nullable();
            $table->string('lap_time', 60)->nullable();
            $table->string('pc_time', 60)->nullable();
            $table->string('tablet_time', 60)->nullable();
            $table->string('movil_time', 60)->nullable();
            $table->string('d_time')->nullable();
            $table->string('d_media', 60)->nullable();
            $table->date('d_test')->nullable();
            $table->tinyInteger('d_fclod')->nullable();
            $table->tinyInteger('d_fcloi')->nullable();
            $table->string('d_fclod_time')->nullable();
            $table->string('d_fcloi_time')->nullable();
            $table->integer('status')->nullable()->default('0');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('contact_id')->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->unsignedBigInteger('category_ii')->nullable();
            $table->foreign('category_ii')->references('id')->on('categories');
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
