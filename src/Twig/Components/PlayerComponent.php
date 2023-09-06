<?php

namespace App\Twig\Components;

use App\Entity\Player;
use LogicException;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class PlayerComponent
{
    public Player $player;
    public bool $isHost = false;
    public bool $isMe = false;

    public function getPlayerClass(): string
    {
        return match ($this->player->getNumber()) {
            1 => 'bg-primary',
            2 => 'bg-success',
            3 => 'bg-danger',
            4 => 'bg-warning',
            default => throw new LogicException('Invalid player number'),
        };
    }
}
