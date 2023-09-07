<?php

namespace App\Service;

use App\Entity\Player;

class GameFunctions
{
    public function start(Player $player): void
    {
        $player->addMoney(200);
    }
}
