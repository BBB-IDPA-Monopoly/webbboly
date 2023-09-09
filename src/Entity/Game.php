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

    #[ORM\Column(nullable: true)]
    private array|null $turnOrder = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Player::class)]
    private Collection $players;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: GameBuilding::class)]
    private Collection $gameBuildings;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: GameActionField::class)]
    private Collection $gameActionFields;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: GameCard::class)]
    private Collection $gameCards;

    #[ORM\ManyToOne]
    private Player|null $currentTurnPlayer = null;

    #[ORM\Column(nullable: true)]
    private int|null $funds = null;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->gameBuildings = new ArrayCollection();
        $this->gameActionFields = new ArrayCollection();
        $this->gameCards = new ArrayCollection();
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

    public function getTurnOrder(): ArrayCollection|null
    {
        if ($this->turnOrder) {
            return new ArrayCollection($this->turnOrder);
        }

        return null;
    }

    public function setTurnOrder(ArrayCollection|null $turnOrder): static
    {
        $this->turnOrder = $turnOrder?->toArray();

        return $this;
    }

    /**
     * @return Collection<int, GameBuilding>
     */
    public function getGameBuildings(): Collection
    {
        return $this->gameBuildings;
    }

    public function addBuilding(GameBuilding $building): static
    {
        if (!$this->gameBuildings->contains($building)) {
            $this->gameBuildings->add($building);
            $building->setGame($this);
        }

        return $this;
    }

    public function removeBuilding(GameBuilding $building): static
    {
        if ($this->gameBuildings->removeElement($building) && $building->getGame() === $this) {
            $building->setGame(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, GameActionField>
     */
    public function getGameActionFields(): Collection
    {
        return $this->gameActionFields;
    }

    public function addActionField(GameActionField $actionField): static
    {
        if (!$this->gameActionFields->contains($actionField)) {
            $this->gameActionFields->add($actionField);
            $actionField->setGame($this);
        }

        return $this;
    }

    public function removeActionField(GameActionField $actionField): static
    {
        if ($this->gameActionFields->removeElement($actionField) && $actionField->getGame() === $this) {
            $actionField->setGame(null);
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
            $card->setGame($this);
        }

        return $this;
    }

    public function removeCard(GameCard $card): static
    {
        if ($this->gameCards->removeElement($card) && $card->getGame() === $this) {
            $card->setGame(null);
        }

        return $this;
    }

    /**
     * @return array<int, GameField>
     */
    public function getFieldsWithPositions(): array
    {
        $fields = [];

        foreach ($this->getGameBuildings() as $gameBuilding) {
            $fields[$gameBuilding->getBuilding()->getPosition()] = $gameBuilding;
        }

        foreach ($this->getGameActionFields() as $gameActionField) {
            $fields[$gameActionField->getActionField()->getPosition()] = $gameActionField;
        }

        ksort($fields);

        return $fields;
    }

    /**
     * @throws Exception
     */
    public function getFieldByPosition(int $position): GameField
    {
        $fields = $this->getFieldsWithPositions();

        if (isset($fields[$position])) {
            return $fields[$position];
        }

        throw new Exception('Field not found');
    }

    public function getGameFieldByField(Field $field): GameField|null
    {
        foreach ($this->getFieldsWithPositions() as $gameField) {
            if ($gameField->getField() === $field) {
                return $gameField;
            }
        }

        return null;
    }

    public function getCurrentTurnPlayer(): Player|null
    {
        return $this->currentTurnPlayer;
    }

    public function setCurrentTurnPlayer(Player|null $currentTurnPlayer): static
    {
        $this->currentTurnPlayer = $currentTurnPlayer;

        return $this;
    }

    public function getFunds(): int|null
    {
        return $this->funds;
    }

    public function setFunds(int|null $funds): static
    {
        $this->funds = $funds;

        return $this;
    }

    public function addFunds(int $funds): static
    {
        $this->funds += $funds;

        return $this;
    }
}
