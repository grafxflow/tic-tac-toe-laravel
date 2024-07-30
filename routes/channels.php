<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('new-game-channel', function () {
    return true;
});

Broadcast::channel('game-channel.{user}.{id}', function (User $user, int $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('game-channel-over.{user}.{id}', function (User $user, int $id) {
    return (int) $user->id === (int) $id;
});

/*
Broadcast::channel('game-over', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
*/
