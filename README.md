# Tic-Tac-Toe Game

Used:

* Laravel 11
* InertiaJs
* Tailwind
* [Docker App](https://www.docker.com)
* Laravel Sail

It is a Tic-Tac-Toe game between 2 registered users. Any user can create/invite another user to play a game.

It also broadcasts events via WebSocket API's during gameplay which allows for both players to be updated once another player has made their move.

## Setup

> NOTE: Make sure DOCKER is installed and running first, plus add the reverb settings to the **.env**.

First install all the dependencies.
```
composer install
```

```
npm install
```

Next migrate the database.

```
./vendor/bin/sail up -d
```

```
./vendor/bin/sail artisan migrate
```

## Start the WebSockets

The start the WebSocket for broadcasting the live game moves/updates data.

```
./vendor/bin/sail artisan reverb:start --debug
```

```
./vendor/bin/sail artisan queue:work
```

Start compiling the frontend.

```
npm run dev
```

Add 2 Users Accounts and test in 2 different browsers.

```
./vendor/bin/sail artisan tinker
```

```
$user = new App\Models\User();
$user->email = 'the-email@example.com';
$user->password = Hash::make('the-password-of-choice');
$user->name = 'Admin User';
$user->save();

$user = new App\Models\User();
$user->email = 'the-email@example2.com';
$user->password = Hash::make('the-password-of-choice2');
$user->name = 'Admin User2';
$user->save();

exit
```
