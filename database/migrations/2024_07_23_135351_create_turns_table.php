<?php

use App\Models\Game;
use App\Models\User;
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
        Schema::create('turns', function (Blueprint $table) {
            $table->id();
            $table->integer('turn_order')->unsigned();
            $table->foreignIdFor(Game::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->enum('location', [1, 2, 3, 4, 5, 6, 7, 8, 9])->nullable();
            $table->enum('type', ['x', 'o']);
            $table->primary(['id', 'game_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turns');
    }
};
