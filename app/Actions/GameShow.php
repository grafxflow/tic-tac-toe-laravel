<?php

namespace App\Actions;

use App\Models\Game;
use App\Models\Turn;
use App\Models\User;
use App\Traits\GameLocationsTrait;
use App\Traits\GameMovesStatusTrait;
use App\Traits\GameOverTrait;
use Illuminate\Http\Request;
use stdClass;

class GameShow
{
    use GameLocationsTrait;
    use GameMovesStatusTrait;
    use GameOverTrait;

    public function handle(Request $request, User $user)
    {
        $players = Turn::where('game_id', '=', $request->gameId)
            ->select('user_id', 'type')
            ->distinct()
            ->get();

        $playerType = $request->user()->id == $players[0]->user_id ? $players[0]->type : $players[1]->type;

        $otherPlayerId = $request->user()->id == $players[0]->user_id ? $players[1]->user_id : $players[0]->user_id;

        $locations = $this->getLocations($request->gameId);

        // Check to see if there is a winning or draw game with the players moves
        $gameMoveStatus = $this->checkGameMovesStatus($locations);

        if ($gameMoveStatus) {
            $this->gameOver($request->location, $request->gameId, $gameMoveStatus, $request->user()->id);
        }

        $nextTurn = Turn::where('game_id', '=', $request->gameId)
            ->whereNull('location')
            ->orderBy('turn_order')
            ->first();

        $game = new stdClass;
        $game->nextTurn = $nextTurn;
        $game->locations = $locations;
        $game->playerType = $playerType;
        $game->otherPlayerId = $otherPlayerId;

        return $game;
    }
}
