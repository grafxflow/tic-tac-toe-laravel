<?php

namespace App\Traits;

use App\Models\Game;
use App\Models\Turn;

trait GameLocationsTrait
{
    public function getLocations(int $gameId)
    {
        $pastTurns = Turn::where('game_id', '=', $gameId)
        ->whereNotNull('location')
        ->orderBy('turn_order')
        ->get();

        $gameActive = Game::whereId($gameId)->finishedGame($gameId)->count();

        $locations = [];

        // Create default tic-tac-toe board locations and types
        for($i = 1; $i <= 9; $i++) {
            $locations[$i]['checked'] = !empty($gameActive) ? true : false;
            $locations[$i]['type'] = '';
        }

        // Update state and type of locations
        foreach ($pastTurns as $pastTurn) {
            $locations[$pastTurn->location]['checked'] = true;
            $locations[$pastTurn->location]['type'] = $pastTurn->type;
        }

        return $locations;
    }
}
