<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_lifestyle', function (Blueprint $table) {
            $table->id();

            // Relación con examen
            $table->foreignId('exam_id')
                ->constrained('exams')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Dispositivos
            |--------------------------------------------------------------------------
            */
            $table->tinyInteger('pc')->nullable();
            $table->string('pc_time')->nullable();

            $table->tinyInteger('lap')->nullable();
            $table->string('lap_time')->nullable();

            $table->tinyInteger('tablet')->nullable();
            $table->string('tablet_time')->nullable();

            $table->tinyInteger('movil')->nullable();
            $table->string('movil_time')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Sintomatología – Cefalea y dolor
            |--------------------------------------------------------------------------
            */
            $table->tinyInteger('cefalea')->nullable();
            $table->string('c_frecuencia')->nullable();
            $table->unsignedTinyInteger('c_intensidad')->nullable(); // 0–4

            $table->tinyInteger('frontal')->nullable();
            $table->tinyInteger('temporal')->nullable();
            $table->tinyInteger('occipital')->nullable();

            $table->tinyInteger('temporaoi')->nullable();
            $table->tinyInteger('temporaod')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Antecedentes
            |--------------------------------------------------------------------------
            */
            $table->text('interrogatorio')->nullable();
            $table->text('coa')->nullable();
            $table->text('aopp')->nullable();
            $table->text('aopf')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Diabetes
            |--------------------------------------------------------------------------
            */
            $table->string('d_media')->nullable();
            $table->date('d_test')->nullable();

            $table->tinyInteger('d_fclod')->nullable();
            $table->string('d_fclod_time')->nullable();

            $table->tinyInteger('d_fcloi')->nullable();
            $table->string('d_fcloi_time')->nullable();

            $table->string('d_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_lifestyle');
    }
};
