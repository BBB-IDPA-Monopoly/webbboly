<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(type: 'smallint')]
    private int|null $number = null;

    #[ORM\Column(length: 20)]
    private string|null $nickname = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isReady = false;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private Game|null $game = null;

    #[ORM\OneToMany(mappedBy: 'Owner', targetEntity: GameBuilding::class)]
    private Collection $properties;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: GameCard::class)]
    private Collection $cards;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
        $this->cards = new ArrayCollection();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getNumber(): int|null
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getNickname(): string|null
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function isReady(): bool
    {
        return $this->isReady;
    }

    public function setReady(bool $isReady): static
    {
        $this->isReady = $isReady;

        return $this;
    }

    public function getGame(): Game|null
    {
        return $this->game;
    }

    public function setGame(Game|null $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function isHost(): bool
    {
        return $this->game->getHost() === $this;
    }

    /**
     * @return Collection<int, GameBuilding>
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(GameBuilding $property): static
    {
        if (!$this->properties->contains($property)) {
            $this->properties->add($property);
            $property->setOwner($this);
        }

        return $this;
    }

    public function removeProperty(GameBuilding $property): static
    {
        if ($this->properties->removeElement($property) && $property->getOwner() === $this) {
            $property->setOwner(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, GameCard>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(GameCard $card): static
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
            $card->setOwner($this);
        }

        return $this;
    }

    public function removeCard(GameCard $card): static
    {
        if ($this->cards->removeElement($card) && $card->getOwner() === $this) {
            $card->setOwner(null);
        }

        return $this;
    }
}
