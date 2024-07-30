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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('winner_id')->unsigned()->nullable();
            $table->foreign('winner_id')->references('id')->on('users');
            $table->bigInteger('player_1_user_id')->unsigned();
            $table->foreign('player_1_user_id')->references('id')->on('users');
            $table->bigInteger('player_2_user_id')->unsigned();
            $table->foreign('player_2_user_id')->references('id')->on('users');
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
