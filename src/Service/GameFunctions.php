<?php

namespace App\Service;

use App\Entity\Player;

final class GameFunctions
{
    public function start(Player $player): void
    {
        $player->addMoney(200);
    }
}
