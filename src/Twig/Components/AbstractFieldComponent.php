<?php

namespace App\Twig\Components;

use App\Entity\Field;
use App\Entity\Game;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use LogicException;

abstract class AbstractFieldComponent
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
    ) {}

    public function getClasses(): string
    {
        $position = $this->getField()->getPosition();

        if ($position > 10 && $position < 20) {
            return 'cell cell-rotated-90';
        } elseif ($position > 20 && $position < 30) {
            return 'cell cell-rotated-180';
        } elseif ($position > 30 && $position < 40) {
            return 'cell cell-rotated-270';
        } else {
            return 'cell';
        }
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array
    {
        $game = $this->getGame();
        $position = $this->getField()->getPosition();

        return $this->playerRepository->findBy(compact('game', 'position'));
    }

    public function getPlayerClass(Player $player): string
    {
        return match ($player->getNumber()) {
            1 => 'primary',
            2 => 'success',
            3 => 'danger',
            4 => 'warning',
            default => throw new LogicException('Invalid player number'),
        };
    }

    public function getGame(): Game
    {
        return $this->field->getGame();
    }

    public function getField(): Field
    {
        return $this->field->getField();
    }
}
