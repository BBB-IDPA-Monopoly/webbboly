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

    public function getId(): int|null
    {
        return $this->id;
    }

    abstract public function getField(): Field;
    abstract public function getOwner(): Player|null;
    abstract public function setOwner(Player|null $owner): static;
}
