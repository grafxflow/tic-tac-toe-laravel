<?php

namespace App\Actions;

use App\Models\Turn;
use App\Models\User;
use App\Events\Play;
use App\Traits\GameLocationsTrait;
use App\Traits\GameMovesStatusTrait;
use App\Traits\GameOverTrait;
use Illuminate\Http\Request;

class GamePlay
{
    use GameLocationsTrait;
    use GameMovesStatusTrait;
    use GameOverTrait;

    public function handle(Request $request, User $user)
    {
        $turn = Turn::where('game_id', '=', $request->game_id)
        ->where('user_id', '=', $user->id)
        ->whereNull('location')
        ->orderBy('turn_order')
        ->first();

        $turn->location = $request->location;
        $turn->save();

        $player = Turn::getOpposingPlayer($user->id, $request->game_id);

        $locations = $this->getLocations($request->game_id);

        // Check to see if there is a winning or draw game with the players moves
        $gameMoveStatus = $this->checkGameMovesStatus($locations);

        if($gameMoveStatus)
        {
            $this->gameOver($request->location, $request->game_id, $gameMoveStatus, $user->id);
            // Refresh Locations due to games status has changed
            $locations = $this->getLocations($request->game_id);
        }

        Play::dispatch($request->game_id, $turn->type, $request->location, $player->id, $locations);
    }
}
