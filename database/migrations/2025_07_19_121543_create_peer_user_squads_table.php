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
        Schema::create('peer_user_squads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peer_user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('star_rating'); // 1 to 5
            $table->foreignId('main_player_id')->constrained('players');
            $table->foreignId('sub_player_id')->constrained('players');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peer_user_squads');
    }
};
