<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_statistics', function (Blueprint $table) {
            $table->unsignedBigInteger('fixture_id')->nullable()->after('player_id');
            $table->unsignedBigInteger('team_id')->nullable()->after('fixture_id');
            $table->integer('minutes')->nullable();
            $table->integer('number')->nullable();
            $table->string('position')->nullable();
            $table->string('rating')->nullable();
            $table->boolean('captain')->nullable();
            $table->boolean('substitute')->nullable();
            $table->integer('offsides')->nullable();
            $table->integer('shots_total')->nullable();
            $table->integer('shots_on')->nullable();
            $table->integer('goals_total')->nullable();
            $table->integer('goals_conceded')->nullable();
            $table->integer('goals_assists')->nullable();
            $table->integer('goals_saves')->nullable();
            $table->integer('passes_total')->nullable();
            $table->integer('passes_key')->nullable();
            $table->string('passes_accuracy')->nullable();
            $table->integer('tackles_total')->nullable();
            $table->integer('tackles_blocks')->nullable();
            $table->integer('tackles_interceptions')->nullable();
            $table->integer('duels_total')->nullable();
            $table->integer('duels_won')->nullable();
            $table->integer('dribbles_attempts')->nullable();
            $table->integer('dribbles_success')->nullable();
            $table->integer('dribbles_past')->nullable();
            $table->integer('fouls_drawn')->nullable();
            $table->integer('fouls_committed')->nullable();
            $table->integer('cards_yellow')->nullable();
            $table->integer('cards_red')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('player_statistics', function (Blueprint $table) {
            $table->dropColumn([
                'fixture_id',
                'team_id',
                'minutes',
                'number',
                'position',
                'rating',
                'captain',
                'substitute',
                'offsides',
                'shots_total',
                'shots_on',
                'goals_total',
                'goals_conceded',
                'goals_assists',
                'goals_saves',
                'passes_total',
                'passes_key',
                'passes_accuracy',
                'tackles_total',
                'tackles_blocks',
                'tackles_interceptions',
                'duels_total',
                'duels_won',
                'dribbles_attempts',
                'dribbles_success',
                'dribbles_past',
                'fouls_drawn',
                'fouls_committed',
                'cards_yellow',
                'cards_red',
            ]);
        });
    }
};
