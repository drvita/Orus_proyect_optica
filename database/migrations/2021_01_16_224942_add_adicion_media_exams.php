<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdicionMediaExams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->decimal('adicion_media_oi', 8, 2)
                ->after('adicioni')
                ->nullable();
            $table->decimal('adicion_media_od', 8, 2)
                ->after('adiciond')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('adicion_media_oi');
            $table->dropColumn('adicion_media_od');
        });
    }
}
