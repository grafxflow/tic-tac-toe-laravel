<?php

namespace App\Listeners;

use App\Events\Play;

class PlayListener
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(Play $event): void {}
}
