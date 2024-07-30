<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    use HasFactory;

    // public $incrementing = false;

    public $fillable = [
        'user_id',
        'location',
        'type',
        'game_id',
        'turn_order',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public static function getOpposingPlayer($userId, $gameId)
    {
        $turn = Turn::where('game_id', '=', $gameId)
        ->select('user_id', 'type')
        ->where('user_id', '!=', $userId)
        ->distinct()
        ->first();

        return User::find($turn->user_id);
    }
}
