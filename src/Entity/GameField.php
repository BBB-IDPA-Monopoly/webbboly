<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

class GameField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected int|null $id = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Player::class)]
    protected Collection $visitors;

    public function __construct()
    {
        $this->visitors = new ArrayCollection();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getVisitors(): Collection
    {
        return $this->visitors;
    }

    public function addVisitor(Player $visitor): static
    {
        if (!$this->visitors->contains($visitor)) {
            $this->visitors[] = $visitor;
        }

        return $this;
    }

    public function removeVisitor(Player $visitor): static
    {
        $this->visitors->removeElement($visitor);

        return $this;
    }
}
