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
        Schema::create('daily_contest_user_squards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_contest_user_id')->constrained('daily_contest_users')->cascadeOnDelete();
            $table->tinyInteger('star_rating');
            $table->foreignId('main_player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('sub_player_id')->constrained('players')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_contest_user_squards');
    }
};
