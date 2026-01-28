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
        Schema::create('exam_functions', function (Blueprint $table) {
            $table->id();

            // Relación con examen
            $table->foreignId('exam_id')
                ->constrained('exams')
                ->cascadeOnDelete();

            // Pruebas funcionales binoculares
            $table->string('fll')->nullable();
            $table->string('fvl')->nullable();
            $table->string('vvl')->nullable();
            $table->string('bnl')->nullable();
            $table->string('btl')->nullable();

            $table->string('ccf')->nullable();
            $table->string('arn')->nullable();
            $table->string('arp')->nullable();
            $table->string('add')->nullable();

            $table->string('flc')->nullable();
            $table->string('flc_100')->nullable();

            $table->string('aca_a')->nullable();
            $table->string('fvc')->nullable();
            $table->string('vvc')->nullable();
            $table->string('bnc')->nullable();
            $table->string('btc')->nullable();

            // Facilidad acomodativa
            $table->string('fa_ao')->nullable();
            $table->string('fa_od')->nullable();
            $table->string('fa_oi')->nullable();

            // Punto próximo de convergencia
            $table->string('ppcn')->nullable();
            $table->string('ppca')->nullable();

            // Amplitud acomodativa negativa
            $table->string('aa_neg_od')->nullable();
            $table->string('aa_neg_oi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_functions');
    }
};
