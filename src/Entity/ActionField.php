<?php

namespace App\Entity;

use App\Repository\ActionFieldRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActionFieldRepository::class)]
class ActionField extends Field
{
    #[ORM\Column(length: 255)]
    private string|null $function = null;

    #[ORM\Column(nullable: true)]
    private int|null $mortgage = null;

    public function getFunction(): string|null
    {
        return $this->function;
    }

    public function setFunction(string $function): static
    {
        $this->function = $function;

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
}
