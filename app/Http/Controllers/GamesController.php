<?php

namespace App\Http\Controllers;

use App\Actions\GamePlay;
use App\Actions\GamesActive;
use App\Actions\GamesFinished;
use App\Actions\GameShow;
use App\Actions\GameStore;
use App\Actions\GamesUsers;
use App\Http\Requests\GameStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GamesController extends Controller
{
    /**
     * Display a listing of current active games for auth user.
     */
    public function index(Request $request, GamesActive $gamesActive): Response
    {
        $games = $gamesActive->handle($request, $request->user());

        return Inertia::render('Game/Index', [
            'games' => $games,
        ]);
    }

    /**
     * Display a listing of finished games for auth user.
     */
    public function finished(Request $request, GamesFinished $gamesFinished): Response
    {
        $games = $gamesFinished->handle($request, $request->user());

        return Inertia::render('Game/Finished', [
            'games' => $games,
            'user' => $request->user(),
        ]);
    }

    /**
     * Display a listing of Users to send new game invite.
     */
    public function create(Request $request, GamesUsers $gamesUsers): Response
    {
        $users = $gamesUsers->handle($request, $request->user());

        return Inertia::render('Game/Create', [
            'users' => $users,
            'user' => $request->user(),
        ]);
    }

    /**
     * Store a newly created game.
     */
    public function store(GameStoreRequest $request, GameStore $gameStore): RedirectResponse
    {
        $game = $gameStore->handle($request, $request->user());

        return redirect()->back();
    }

    /**
     * Play a users game request.
     */
    public function play(Request $request, GamePlay $gamePlay): RedirectResponse
    {
        $game = $gamePlay->handle($request, $request->user());

        return redirect()->back();
    }

    /**
     * Display active game.
     */
    public function show(Request $request, GameShow $gameShow): Response
    {
        $game = $gameShow->handle($request, $request->user());

        return Inertia::render('Game/Show', [
            'user' => $request->user(),
            'id' => $request->gameId,
            'nextTurn' => $game->nextTurn,
            'locations' => $game->locations,
            'playerType' => $game->playerType,
            'otherPlayerId' => $game->otherPlayerId,
        ]);
    }
}
