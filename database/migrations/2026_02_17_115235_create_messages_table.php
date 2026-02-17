<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('type')->default('text');
            $table->text('media')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('messagable_id');
            $table->string('messagable_type');
            $table->timestamps();

            $table->index(['messagable_type', 'messagable_id']);
        });

        // Migrate existing order messages linking them to the Contact (Patient)
        $messengers = DB::table('messengers')
            ->join('orders', 'messengers.idRow', '=', 'orders.id')
            ->where('messengers.table', 'orders')
            ->select('messengers.*', 'orders.contact_id')
            ->get();

        foreach ($messengers as $messenger) {
            DB::table('messages')->insert([
                'message' => $messenger->message,
                'type' => 'text',
                'user_id' => $messenger->user_id,
                'messagable_id' => $messenger->contact_id,
                'messagable_type' => 'App\Models\Contact',
                'created_at' => $messenger->created_at,
                'updated_at' => $messenger->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
