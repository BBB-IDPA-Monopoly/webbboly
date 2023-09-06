<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

abstract class GameField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected int|null $id = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Player::class)]
    protected Collection $players;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addVisitor(Player $visitor): static
    {
        if (!$this->players->contains($visitor)) {
            $this->players[] = $visitor;
        }

        return $this;
    }

    public function removeVisitor(Player $visitor): static
    {
        $this->players->removeElement($visitor);

        return $this;
    }

    abstract public function getField(): Field;
}
