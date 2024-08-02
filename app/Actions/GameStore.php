<?php

namespace App\Actions;

use App\Models\Game;
use App\Models\Turn;
use App\Models\User;
use App\Events\NewGame;
use App\Notifications\NewGameNotification;
use Illuminate\Http\Request;

class GameStore
{
    public function handle(Request $request, User $user)
    {
        // Check if game has already been created
        $newGame = Game::firstOrCreate(
            [
                'player_1_user_id' => $request->user_id,
                'player_2_user_id' => $request->user_invited_id,
                'end_date' => null
            ]
        );

        if($newGame) {

            for($i = 1; $i <= 9; $i++) {
                Turn::firstOrCreate(
                    [
                        'game_id' => $newGame->id,
                        'turn_order' => $i,
                        'type' => $i % 2 ? 'x' : 'o',
                        'user_id' => $i % 2 ? $request->user_id : $request->user_invited_id,
                    ]
                );
            }

            NewGame::dispatch($request->user_invited_id, $newGame, $user);

            // Send Notification to other User that a game has been created
            User::find($request->user_invited_id)
            ->notify(new NewGameNotification($newGame, User::find($request->user_invited_id)));

            $newGame->users()->sync([
                $request->user_id,
                $request->user_invited_id
            ]);
        }
    }
}
