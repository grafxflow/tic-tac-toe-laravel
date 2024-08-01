<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Turn;
use App\Models\User;
use App\Events\NewGame;
use App\Events\Play;
use App\Events\GameOver;
use App\Http\Requests\GameStoreRequest;
use App\Notifications\NewGameNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Builder;

class GamesController extends Controller
{
    const WIN = 'win';
    const DRAW = 'draw';

    /**
     * Display a listing of current active games for auth user.
     */
    public function index(Request $request): Response
    {
        $games = $request->user()->activeGames()
        ->paginate(10)
        ->through(function ($game) use ($request) {
            return [
                'id' => $game->id,
                'opposing_player' => Turn::getOpposingPlayer($request->user()->id, $game->id)->email,
                'created_at' => Carbon::CreateFromFormat('Y-m-d H:i:s', $game->created_at)->format('d/m/Y H:i:s'),
            ];
        });

        return Inertia::render('Game/Index', [
            'games' => $games,
        ]);
    }

    /**
     * Display a listing of finished games for auth user.
     */
    public function finished(Request $request): Response
    {
        $games = $request->user()->finishedGames()
        ->paginate(10)
        ->through(function ($game) {
            return [
                'id' => $game->id,
                'winner_player' => User::find($game->winner_id),
                'created_at' => Carbon::CreateFromFormat('Y-m-d H:i:s', $game->created_at)->format('d/m/Y H:i:s'),
            ];
        });

        return Inertia::render('Game/Finished', [
            'games' => $games,
            'user' => request()->user(),
        ]);
    }

    /**
     * Display a listing of users and Create New Game.
     */
    public function create(Request $request): Response
    {
        $user = $request->user();

        $users = User::where('id', '!=', $user->id)
        ->with(['games' => function($query) use ($user) {
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
                'games' => !empty($user->games) ? $user->games->first() : null,
            ];
        });

        return Inertia::render('Game/Create', [
            'users' => $users,
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GameStoreRequest $request)
    {
        $user = $request->user();

        // Check if game has already been created
        $game = Game::firstOrCreate(
            [
                'player_1_user_id' => $request->user_id,
                'player_2_user_id' => $request->user_invited_id,
                'end_date' => null
            ]
        );

        if($game) {

            for($i = 1; $i <= 9; $i++) {
                Turn::firstOrCreate(
                    [
                        'game_id' => $game->id,
                        'turn_order' => $i,
                        'type' => $i % 2 ? 'x' : 'o',
                        'user_id' => $i % 2 ? $request->user_id : $request->user_invited_id,
                    ]
                );
            }

            NewGame::dispatch($request->user_invited_id, $game, $user);

            // Send Notification to other User that a game has been created
            User::find($request->user_invited_id)
            ->notify(new NewGameNotification($game, User::find($request->user_invited_id)));

            $game->users()->sync([
                $request->user_id,
                $request->user_invited_id
            ]);
        }

        return redirect()->back();
    }

    public function play(Request $request)
    {
        $turn = Turn::where('game_id', '=', $request->game_id)
        ->where('user_id', '=', $request->user()->id)
        ->whereNull('location')
        ->orderBy('turn_order')
        ->first();

        $turn->location = $request->location;
        $turn->save();

        $player = Turn::getOpposingPlayer($request->user()->id, $request->game_id);

        $locations = $this->getLocations($request->game_id);

        // Check to see if there is a winning or draw game with the players moves
        $gameMoveStatus = $this->checkGameMovesStatus($locations);

        if($gameMoveStatus)
        {
            $this->gameOver($request->location, $request->game_id, $gameMoveStatus, $request->user()->id);
            // Refresh Locations due to games status has changed
            $locations = $this->getLocations($request->game_id);
        }

        Play::dispatch($request->game_id, $turn->type, $request->location, $player->id, $locations);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): Response
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

        if($gameMoveStatus)
        {
            $this->gameOver($request->location, $request->gameId, $gameMoveStatus, $request->user()->id);
        }

        $nextTurn = Turn::where('game_id', '=', $request->gameId)
        ->whereNull('location')
        ->orderBy('turn_order')
        ->first();

        $game = Game::find($request->gameId);

        return Inertia::render('Game/Show', [
            'user' => $request->user(),
            'id' => $request->gameId,
            'nextTurn' => $nextTurn,
            'locations' => $locations,
            'playerType' => $playerType,
            'otherPlayerId' => $otherPlayerId
        ]);
    }

    /**
     * Find the current games tic-tac-toe selected items.
     */
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

    // $location can be string or null
    public function gameOver($location, int $gameId, string $result, int $userId)
    {
        // $location can be null

        $turn = Turn::where('game_id', '=', $gameId)
        ->where('user_id', '=', $userId)
        ->orderBy('turn_order')
        ->first();

        $player = Turn::getOpposingPlayer($userId, $gameId);

        if($result == 'win')
        {
            Game::where('id', $gameId)
            ->update([
                'winner_id' => $userId,
                'end_date' => Carbon::now()->toDateTimeString(),
                ]
            );
        } else {
            Game::where('id', $gameId)
            ->update([
                'end_date' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        GameOver::dispatch($gameId, $player->id, $result, $location, $turn->type, $userId);
        GameOver::dispatch($gameId, $userId, $result, $location, $turn->type, $userId);

        return redirect()->back();
    }

    public function checkGameMovesStatus(array $locations)
    {
        // Loop through each x and o and check for the winner
        foreach (['x', 'o'] as $type)
        {
            if ($locations[1]['type'] == $type && $locations[2]['type'] == $type && $locations[3]['type'] == $type
                || $locations[4]['type'] == $type && $locations[5]['type'] == $type && $locations[6]['type'] == $type
                || $locations[7]['type'] == $type && $locations[8]['type'] == $type && $locations[9]['type'] == $type
                || $locations[1]['type'] == $type && $locations[4]['type'] == $type && $locations[7]['type'] == $type
                || $locations[2]['type'] == $type && $locations[5]['type'] == $type && $locations[8]['type'] == $type
                || $locations[3]['type'] == $type && $locations[6]['type'] == $type && $locations[9]['type'] == $type
                || $locations[1]['type'] == $type && $locations[5]['type'] == $type && $locations[9]['type'] == $type
                || $locations[3]['type'] == $type && $locations[5]['type'] == $type && $locations[7]['type'] == $type
            ) {
                return self::WIN;
            }
        }

        // Check for draw
        $draw = 0;

        for($i = 1; $i <= 9; $i++) {
            if($locations[$i]['checked'] == true) {
                $draw++;
            }
            if ($draw == 9) {
                return self::DRAW;
            }
        }

        return false;
    }
}
