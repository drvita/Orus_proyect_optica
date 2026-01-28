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
        Schema::create('exam_clinical', function (Blueprint $table) {
            $table->id();

            // Relación con examen
            $table->foreignId('exam_id')
                ->constrained('exams')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | General
            |--------------------------------------------------------------------------
            */
            $table->string('avf2o')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Ojo Derecho (OD)
            |--------------------------------------------------------------------------
            */
            $table->string('avslod')->nullable();
            $table->string('avcgaod')->nullable();
            $table->string('avfod')->nullable();
            $table->string('piod')->nullable();
            $table->string('keratometriaod')->nullable();
            $table->string('rsod')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Ojo Izquierdo (OI)
            |--------------------------------------------------------------------------
            */
            $table->string('avsloi')->nullable();
            $table->string('avcgaoi')->nullable();
            $table->string('avfoi')->nullable();
            $table->string('pioi')->nullable();
            $table->string('keratometriaoi')->nullable();
            $table->string('rsoi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_clinical');
    }
};
