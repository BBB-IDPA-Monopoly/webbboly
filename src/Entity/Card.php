<?php

namespace App\Entity;

use App\Enum\CardType;
use App\Repository\CardRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private string|null $text = null;

    #[ORM\Column]
    private string|null $type = null;

    #[ORM\Column(length: 255)]
    private string|null $function = null;

    #[ORM\Column]
    private int|null $amountPerGame = null;

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getText(): string|null
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getType(): CardType|null
    {
        return CardType::tryFrom($this->type);
    }

    public function setType(CardType $type): static
    {
        $this->type = $type->value;

        return $this;
    }

    public function getFunction(): string|null
    {
        return $this->function;
    }

    public function setFunction(string $function): static
    {
        $this->function = $function;

        return $this;
    }

    public function getAmountPerGame(): int|null
    {
        return $this->amountPerGame;
    }

    public function setAmountPerGame(int $amountPerGame): static
    {
        $this->amountPerGame = $amountPerGame;

        return $this;
    }
}
