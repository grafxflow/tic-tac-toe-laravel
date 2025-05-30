<?php

namespace App\Traits;

trait GameMovesStatusTrait
{
    public const WIN = 'win';

    public const DRAW = 'draw';

    public function checkGameMovesStatus(array $locations)
    {
        // Loop through each x and o and check for the winner
        foreach (['x', 'o'] as $type) {
            if ($locations[1]['type'] === $type && $locations[2]['type'] === $type && $locations[3]['type'] === $type
                || $locations[4]['type'] === $type && $locations[5]['type'] === $type && $locations[6]['type'] === $type
                || $locations[7]['type'] === $type && $locations[8]['type'] === $type && $locations[9]['type'] === $type
                || $locations[1]['type'] === $type && $locations[4]['type'] === $type && $locations[7]['type'] === $type
                || $locations[2]['type'] === $type && $locations[5]['type'] === $type && $locations[8]['type'] === $type
                || $locations[3]['type'] === $type && $locations[6]['type'] === $type && $locations[9]['type'] === $type
                || $locations[1]['type'] === $type && $locations[5]['type'] === $type && $locations[9]['type'] === $type
                || $locations[3]['type'] === $type && $locations[5]['type'] === $type && $locations[7]['type'] === $type
            ) {
                return self::WIN;
            }
        }

        // Check for draw
        $draw = 0;

        for ($i = 1; $i <= 9; $i++) {
            if (true === $locations[$i]['checked']) {
                $draw++;
            }
            if (9 === $draw) {
                return self::DRAW;
            }
        }

        return false;
    }
}
