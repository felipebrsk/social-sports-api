<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_time');
            $table->unsignedInteger('max_players');
            $table->text('description')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->boolean('featured')->default(false);
            $table->unsignedInteger('external_players_count')->default(0);
            $table->foreignId('venue_id')->constrained();
            $table->foreignId('sport_id')->constrained();
            $table->foreignId('game_session_status_id')->constrained();
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('skill_level_id')->nullable()->constrained();
            $table->foreignId('host_team_id')->nullable()->constrained('teams');
            $table->foreignId('visitor_team_id')->nullable()->constrained('teams');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
