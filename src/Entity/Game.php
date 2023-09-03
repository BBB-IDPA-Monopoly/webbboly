<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[UniqueEntity(fields: ['code'])]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column]
    private int|null $code = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Player::class)]
    private Collection $players;

    #[ORM\OneToMany(mappedBy: 'Game', targetEntity: GameBuilding::class)]
    private Collection $buildings;

    #[ORM\OneToMany(mappedBy: 'Game', targetEntity: GameActionField::class)]
    private Collection $actionFields;

    #[ORM\OneToMany(mappedBy: 'Game', targetEntity: GameCard::class)]
    private Collection $card;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->buildings = new ArrayCollection();
        $this->actionFields = new ArrayCollection();
        $this->card = new ArrayCollection();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getCode(): int|null
    {
        return $this->code;
    }

    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     * @throws Exception
     */
    public function getPlayers(): Collection
    {
        $iterator = $this->players->getIterator();
        $iterator->uasort(static fn (Player $a, Player $b) => $a->getNumber() <=> $b->getNumber());

        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function getPlayerByNickname(string $nickname)
    {
        return $this->players->filter(static fn (Player $player) => $player->getNickname() === $nickname)->first() ?: null;
    }

    public function addPlayer(Player $player): static
    {
        if (!$this->players->contains($player) && !$this->isGameFull()) {
            $this->players->add($player);
        }

        return $this;
    }

    public function removePlayer(Player $player): static
    {
        if ($this->players->removeElement($player) && $player->getGame() === $this) {
            $player->setGame(null);
        }

        return $this;
    }

    public function getHost(): Player|null
    {
        return $this->players->filter(static fn (Player $player) => $player->getNumber() === 1)->first() ?: null;
    }

    public function isGameFull(): bool
    {
        return $this->players->count() >= 4;
    }

    public function allPlayersReady(): bool
    {
        foreach ($this->players as $player) {
            if (!$player->isReady()) {
                return false;
            }
        }

        return true;
    }

    public function isGameReady(): bool
    {
        return $this->isGameFull() && $this->allPlayersReady();
    }

    /**
     * @return Collection<int, GameBuilding>
     */
    public function getBuildings(): Collection
    {
        return $this->buildings;
    }

    public function addBuilding(GameBuilding $building): static
    {
        if (!$this->buildings->contains($building)) {
            $this->buildings->add($building);
            $building->setGame($this);
        }

        return $this;
    }

    public function removeBuilding(GameBuilding $building): static
    {
        if ($this->buildings->removeElement($building) && $building->getGame() === $this) {
            $building->setGame(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, GameActionField>
     */
    public function getActionFields(): Collection
    {
        return $this->actionFields;
    }

    public function addActionField(GameActionField $actionField): static
    {
        if (!$this->actionFields->contains($actionField)) {
            $this->actionFields->add($actionField);
            $actionField->setGame($this);
        }

        return $this;
    }

    public function removeActionField(GameActionField $actionField): static
    {
        if ($this->actionFields->removeElement($actionField) && $actionField->getGame() === $this) {
            $actionField->setGame(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, GameCard>
     */
    public function getCard(): Collection
    {
        return $this->card;
    }

    public function addCard(GameCard $card): static
    {
        if (!$this->card->contains($card)) {
            $this->card->add($card);
            $card->setGame($this);
        }

        return $this;
    }

    public function removeCard(GameCard $card): static
    {
        if ($this->card->removeElement($card) && $card->getGame() === $this) {
            $card->setGame(null);
        }

        return $this;
    }
}
