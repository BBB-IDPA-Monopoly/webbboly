<?php

namespace App\Twig\Components;

use App\Entity\Building;
use App\Entity\Player;
use App\Service\GameService;
use LogicException;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class PlayerComponent
{
    public Player $player;
    public bool $isHost = false;
    public bool $isMe = false;
    public bool $isMyTurn = false;

    public function __construct(
        private GameService $gameService,
    ) {}

    public function getPlayerClass(): string
    {
        return match ($this->player->getNumber()) {
            1 => 'primary',
            2 => 'success',
            3 => 'danger',
            4 => 'warning',
            default => throw new LogicException('Invalid player number'),
        };
    }

    public function wholeStreetOwned(Building $building): bool
    {
        return $this->gameService->wholeStreetOwned($this->player->getGame(), $building);
    }
}
