<?php

namespace App\Twig\Components;

use App\Entity\Field;
use App\Entity\Game;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use LogicException;

abstract class AbstractFieldComponent
{
    const ROTATION_0 = 0;
    const ROTATION_90 = 90;
    const ROTATION_180 = 180;
    const ROTATION_270 = 270;

    public int $rotation;

    public function __construct(
        private readonly PlayerRepository $playerRepository,
    ) {}

    public function getClasses(): string
    {
        return match ($this->rotation) {
            self::ROTATION_0 => 'cell',
            self::ROTATION_90 => 'cell cell-rotated-90',
            self::ROTATION_180 => 'cell cell-rotated-180',
            self::ROTATION_270=> 'cell cell-rotated-270',
            default => throw new LogicException('Invalid rotation'),
        };
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

    abstract public function getField(): Field;
    abstract public function getGame(): Game;
}
