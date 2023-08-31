<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

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
}
