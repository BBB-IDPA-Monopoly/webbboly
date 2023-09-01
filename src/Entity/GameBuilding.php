<?php

namespace App\Entity;

use App\Repository\GameBuildingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameBuildingRepository::class)]
class GameBuilding
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Building|null $building = null;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    private Player|null $Owner = null;

    #[ORM\Column]
    private int $houses = 0;

    #[ORM\Column]
    private bool $mortgaged = false;

    #[ORM\ManyToOne(inversedBy: 'buildings')]
    #[ORM\JoinColumn(nullable: false)]
    private Game|null $Game = null;

    public function getId(): int|null
    {
        return $this->id;
    }

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
        return $this->Owner;
    }

    public function setOwner(Player|null $Owner): static
    {
        $this->Owner = $Owner;

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
        return $this->Game;
    }

    public function setGame(Game|null $Game): static
    {
        $this->Game = $Game;

        return $this;
    }
}
