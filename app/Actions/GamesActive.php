<?php

namespace App\Actions;

use App\Models\Turn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GamesActive
{
    public function handle(Request $request, User $user)
    {
        $games = $user->activeGames()
            ->paginate(10)
            ->through(function ($game) use ($request) {
                return [
                    'id' => $game->id,
                    'opposing_player' => Turn::getOpposingPlayer($request->user()->id, $game->id)->email,
                    'created_at' => Carbon::CreateFromFormat('Y-m-d H:i:s', $game->created_at)->format('d/m/Y H:i:s'),
                ];
            });

        return $games;
    }
}
