<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;

class GamesUsers
{
    public function handle(Request $request, User $user)
    {
        $users = User::where('id', '!=', $user->id)
            ->with(['games' => function ($query) use ($user): void {
                $query->activeGames()
                    ->currentGames($user->id);
            }])
            ->paginate(10)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'game' => $user->games->count(),
                    'games' => ! empty($user->games) ? $user->games->first() : null,
                ];
            });

        return $users;
    }
}
