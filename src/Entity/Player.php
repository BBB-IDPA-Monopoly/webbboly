<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

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

    #[ORM\Column]
    private int $money = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $isReady = false;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private Game|null $game = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: GameBuilding::class)]
    private Collection $gameBuildings;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: GameCard::class)]
    private Collection $gameCards;

    #[ORM\Column(nullable: true)]
    private int|null $position = null;

    public function __construct()
    {
        $this->gameBuildings = new ArrayCollection();
        $this->gameCards = new ArrayCollection();
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

    public function getMoney(): int
    {
        return $this->money;
    }

    public function setMoney(int $money): static
    {
        $this->money = $money;

        return $this;
    }

    public function addMoney(int $money): static
    {
        $this->money += $money;

        return $this;
    }

    public function subtractMoney(int $money): static
    {
        $this->money -= $money;

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
     * @throws Exception
     */
    public function getGameBuildings(): Collection
    {
        $iterator = $this->gameBuildings->getIterator();
        $iterator->uasort(static function (GameBuilding $a, GameBuilding $b) {
            return $a->getBuilding()->getPosition() <=> $b->getBuilding()->getPosition();
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function addProperty(GameBuilding $property): static
    {
        if (!$this->gameBuildings->contains($property)) {
            $this->gameBuildings->add($property);
            $property->setOwner($this);
        }

        return $this;
    }

    public function removeProperty(GameBuilding $property): static
    {
        if ($this->gameBuildings->removeElement($property) && $property->getOwner() === $this) {
            $property->setOwner(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, GameCard>
     */
    public function getGameCards(): Collection
    {
        return $this->gameCards;
    }

    public function addCard(GameCard $card): static
    {
        if (!$this->gameCards->contains($card)) {
            $this->gameCards->add($card);
            $card->setOwner($this);
        }

        return $this;
    }

    public function removeCard(GameCard $card): static
    {
        if ($this->gameCards->removeElement($card) && $card->getOwner() === $this) {
            $card->setOwner(null);
        }

        return $this;
    }

    public function getPosition(): int|null
    {
        return $this->position;
    }

    public function setPosition(int|null $position): static
    {
        $this->position = $position;

        return $this;
    }
}
