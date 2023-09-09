<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class Field
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected int|null $id = null;

    #[ORM\Column(length: 255)]
    protected string|null $name = null;

    #[ORM\Column]
    protected int|null $position = null;

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

    public function getPosition(): int|null
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}
