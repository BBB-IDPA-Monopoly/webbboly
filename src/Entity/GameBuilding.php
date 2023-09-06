<?php

namespace App\Entity;

use App\Repository\GameBuildingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameBuildingRepository::class)]
class GameBuilding extends GameField
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Building|null $building = null;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    private Player|null $owner = null;

    #[ORM\Column]
    private int $houses = 0;

    #[ORM\Column]
    private bool $mortgaged = false;

    #[ORM\ManyToOne(inversedBy: 'buildings')]
    #[ORM\JoinColumn(nullable: false)]
    private Game|null $game = null;

    public function getBuilding(): Building|null
    {
        return $this->building;
    }

    public function setBuilding(Building|null $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function getOwner(): Player|null
    {
        return $this->owner;
    }

    public function setOwner(Player|null $Owner): static
    {
        $this->owner = $Owner;

        return $this;
    }

    public function getHouses(): int|null
    {
        return $this->houses;
    }

    public function setHouses(int $houses): static
    {
        $this->houses = $houses;

        return $this;
    }

    public function isMortgaged(): bool|null
    {
        return $this->mortgaged;
    }

    public function setMortgaged(bool $mortgaged): static
    {
        $this->mortgaged = $mortgaged;

        return $this;
    }

    public function getGame(): Game|null
    {
        return $this->game;
    }

    public function setGame(Game|null $Game): static
    {
        $this->game = $Game;

        return $this;
    }
}
