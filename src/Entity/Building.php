<?php

namespace App\Entity;

use App\Repository\BuildingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
class Building
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 255)]
    private string|null $name = null;

    #[ORM\Column]
    private int|null $unitRent = null;

    #[ORM\Column]
    private int|null $streetRent = null;

    #[ORM\Column]
    private int|null $singleHouseRent = null;

    #[ORM\Column]
    private int|null $doubleHouseRent = null;

    #[ORM\Column]
    private int|null $tripleHouseRent = null;

    #[ORM\Column]
    private int|null $quadrupleHouseRent = null;

    #[ORM\Column]
    private int|null $hotelRent = null;

    #[ORM\Column]
    private int|null $mortgage = null;

    #[ORM\Column]
    private int|null $mortgageFee = null;

    #[ORM\Column]
    private int|null $position = null;

    #[ORM\ManyToOne(inversedBy: 'buildings')]
    #[ORM\JoinColumn(nullable: false)]
    private Street|null $Street = null;

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getName(): string|null
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUnitRent(): int|null
    {
        return $this->unitRent;
    }

    public function setUnitRent(int $unitRent): static
    {
        $this->unitRent = $unitRent;

        return $this;
    }

    public function getStreetRent(): int|null
    {
        return $this->streetRent;
    }

    public function setStreetRent(int $streetRent): static
    {
        $this->streetRent = $streetRent;

        return $this;
    }

    public function getSingleHouseRent(): int|null
    {
        return $this->singleHouseRent;
    }

    public function setSingleHouseRent(int $singleHouseRent): static
    {
        $this->singleHouseRent = $singleHouseRent;

        return $this;
    }

    public function getDoubleHouseRent(): int|null
    {
        return $this->doubleHouseRent;
    }

    public function setDoubleHouseRent(int $doubleHouseRent): static
    {
        $this->doubleHouseRent = $doubleHouseRent;

        return $this;
    }

    public function getTripleHouseRent(): int|null
    {
        return $this->tripleHouseRent;
    }

    public function setTripleHouseRent(int $tripleHouseRent): static
    {
        $this->tripleHouseRent = $tripleHouseRent;

        return $this;
    }

    public function getQuadrupleHouseRent(): int|null
    {
        return $this->quadrupleHouseRent;
    }

    public function setQuadrupleHouseRent(int $quadrupleHouseRent): static
    {
        $this->quadrupleHouseRent = $quadrupleHouseRent;

        return $this;
    }

    public function getHotelRent(): int|null
    {
        return $this->hotelRent;
    }

    public function setHotelRent(int $hotelRent): static
    {
        $this->hotelRent = $hotelRent;

        return $this;
    }

    public function getMortgage(): int|null
    {
        return $this->mortgage;
    }

    public function setMortgage(int $mortgage): static
    {
        $this->mortgage = $mortgage;

        return $this;
    }

    public function getMortgageFee(): int|null
    {
        return $this->mortgageFee;
    }

    public function setMortgageFee(int $mortgageFee): static
    {
        $this->mortgageFee = $mortgageFee;

        return $this;
    }

    public function getPosition(): int|null
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getStreet(): Street|null
    {
        return $this->Street;
    }

    public function setStreet(Street|null $Street): static
    {
        $this->Street = $Street;

        return $this;
    }
}
