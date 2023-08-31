<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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

    public function __construct()
    {
        $this->players = new ArrayCollection();
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
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): static
    {
        if (!$this->players->contains($player) && !$this->isGameFull()) {
            $this->players->add($player);
            $player->setGame($this);
            $player->setNumber($this->players->count());
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
        return $this->players->first() ?: null;
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
}
