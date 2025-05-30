<?php

namespace App\Traits;

use App\Events\GameOver;
use App\Models\Game;
use App\Models\Turn;
use Carbon\Carbon;

trait GameOverTrait
{
    public function gameOver($location, int $gameId, string $result, int $userId): void
    {
        $turn = Turn::where('game_id', '=', $gameId)
            ->where('user_id', '=', $userId)
            ->orderBy('turn_order')
            ->first();

        $player = Turn::getOpposingPlayer($userId, $gameId);

        if ('win' === $result) {
            Game::where('id', $gameId)
                ->update(
                    [
                        'winner_id' => $userId,
                        'end_date' => Carbon::now()->toDateTimeString(),
                    ],
                );
        } else {
            Game::where('id', $gameId)
                ->update(
                    [
                        'end_date' => Carbon::now()->toDateTimeString(),
                    ],
                );
        }

        GameOver::dispatch($gameId, $player->id, $result, $location, $turn->type, $userId);
        GameOver::dispatch($gameId, $userId, $result, $location, $turn->type, $userId);
    }
}
