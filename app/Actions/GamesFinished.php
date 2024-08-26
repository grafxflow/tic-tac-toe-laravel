<?php

namespace App\Actions;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GamesFinished
{
    public function handle(Request $request, User $user)
    {
        $games = $user->finishedGames()
            ->paginate(10)
            ->through(function ($game) {
                return [
                    'id' => $game->id,
                    'winner_player' => User::find($game->winner_id),
                    'created_at' => Carbon::CreateFromFormat('Y-m-d H:i:s', $game->created_at)->format('d/m/Y H:i:s'),
                ];
            });

        return $games;
    }
}
