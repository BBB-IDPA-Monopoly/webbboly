<?php

namespace App\Entity;

use App\Repository\GameActionFieldRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameActionFieldRepository::class)]
class GameActionField extends GameField
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ActionField|null $actionField = null;

    #[ORM\ManyToOne(inversedBy: 'actionFields')]
    #[ORM\JoinColumn(nullable: false)]
    private Game|null $game = null;

    public function getActionField(): ActionField|null
    {
        return $this->actionField;
    }

    public function setActionField(ActionField|null $actionField): static
    {
        $this->actionField = $actionField;

        return $this;
    }

    public function getGame(): Game|null
    {
        return $this->game;
    }

    public function setGame(Game|null $game): static
    {
        $this->game = $game;

        return $this;
    }
}
