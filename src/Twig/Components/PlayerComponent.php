<?php

namespace App\Twig\Components;

use App\Entity\Player;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class PlayerComponent
{
    public Player $player;
    public bool $isHost = false;
    public bool $isMe = false;
}
