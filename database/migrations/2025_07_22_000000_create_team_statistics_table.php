<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fixture_id');
            $table->unsignedBigInteger('team_id');
            $table->integer('shots_on_goal')->nullable();
            $table->integer('shots_off_goal')->nullable();
            $table->integer('total_shots')->nullable();
            $table->integer('blocked_shots')->nullable();
            $table->integer('shots_insidebox')->nullable();
            $table->integer('shots_outsidebox')->nullable();
            $table->integer('fouls')->nullable();
            $table->integer('corner_kicks')->nullable();
            $table->integer('offsides')->nullable();
            $table->string('ball_possession')->nullable();
            $table->integer('yellow_cards')->nullable();
            $table->integer('red_cards')->nullable();
            $table->integer('goalkeeper_saves')->nullable();
            $table->integer('total_passes')->nullable();
            $table->integer('passes_accurate')->nullable();
            $table->string('passes_pct')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_statistics');
    }
};
