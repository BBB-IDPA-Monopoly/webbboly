<?php

namespace App\Entity;

use App\Repository\GameBuildingRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: GameBuildingRepository::class)]
class GameBuilding extends GameField
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Building|null $building = null;

    #[ORM\ManyToOne(inversedBy: 'gameBuildings')]
    protected Player|null $owner = null;

    #[ORM\Column]
    private int $houses = 0;

    #[ORM\Column]
    private bool $mortgaged = false;

    #[ORM\ManyToOne(inversedBy: 'gameBuildings')]
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

    public function setOwner(Player|null $owner): static
    {
        $this->owner = $owner;

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

    public function addHouse(): static
    {
        $this->houses++;

        return $this;
    }

    public function removeHouse(): static
    {
        $this->houses--;

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

    public function getField(): Building
    {
        return $this->getBuilding();
    }

    /**
     * @throws Exception
     */
    public function getRent($wholeStreetOwned = false): int|null
    {
        return match ($this->getHouses()) {
            0 => $wholeStreetOwned ? $this->getBuilding()->getStreetRent() : $this->getBuilding()->getUnitRent(),
            1 => $this->getBuilding()->getSingleHouseRent(),
            2 => $this->getBuilding()->getDoubleHouseRent(),
            3 => $this->getBuilding()->getTripleHouseRent(),
            4 => $this->getBuilding()->getQuadrupleHouseRent(),
            5 => $this->getBuilding()->getHotelRent(),
            default => throw new Exception('Invalid number of houses'),
        };
    }
}
