<?php

namespace App\Entity;

use App\Repository\GameCardRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameCardRepository::class)]
class GameCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Card|null $Card = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    private Player|null $owner = null;

    #[ORM\ManyToOne(inversedBy: 'card')]
    #[ORM\JoinColumn(nullable: false)]
    private Game|null $Game = null;

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getCard(): Card|null
    {
        return $this->Card;
    }

    public function setCard(Card|null $Card): static
    {
        $this->Card = $Card;

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
