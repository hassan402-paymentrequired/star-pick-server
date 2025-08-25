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
        Schema::table('daily_contest_user_squards', function (Blueprint $table) {
            $table->foreignId('main_player_match_id')->nullable()->constrained('player_matches')->cascadeOnDelete();
            $table->foreignId('sub_player_match_id')->nullable()->constrained('player_matches')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_contest_user_squards', function (Blueprint $table) {
            $table->dropForeign(['main_player_match_id']);
            $table->dropForeign(['sub_player_match_id']);
            $table->dropColumn(['main_player_match_id', 'sub_player_match_id']);
        });
    }
};
