<?php

namespace App\Service;

use App\Entity\Player;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class LobbyStreamService
{
    public function __construct(
        private HubInterface $hub,
        private Environment $twig,
    ) {}

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendPlayerJoin(int $code, Player $player): void
    {
        $data = compact('code', 'player');

        $this->send(
            sprintf('https://webbboly/game/%d', $code),
            $this->twig->render('game/stream/lobby/_player-join.stream.html.twig', [
                ...$data,
                'forHost' => false,
        ]));

        $this->send(
            sprintf('https://webbboly/game/%d/host', $code),
            $this->twig->render('game/stream/lobby/_player-join.stream.html.twig', [
                ...$data,
                'forHost' => true,
        ]));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendPlayerReady(int $code, Player $player): void
    {
        $data = compact('code', 'player');

        $this->send(
            sprintf('https://webbboly/game/%d', $code),
            $this->twig->render('game/stream/lobby/_player-ready.stream.html.twig', [
                ...$data,
                'forHost' => false,
        ]));

        $this->send(
            sprintf('https://webbboly/game/%d/host', $code),
            $this->twig->render('game/stream/lobby/_player-ready.stream.html.twig', [
                ...$data,
                'forHost' => true,
        ]));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendPlayerLeave(int $code, Player $player): void
    {
        $data = compact('code', 'player');

        $this->send(
            sprintf('https://webbboly/game/%d', $code),
            $this->twig->render('game/stream/lobby/_player-remove.stream.html.twig', $data)
        );

        $this->send(
            sprintf('https://webbboly/game/%d/player/%s', $code, $player->getNumber()),
            $this->twig->render('game/stream/lobby/_player-leave.stream.html.twig')
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendPlayerKick(int $code, Player $player): void
    {
        $data = compact('code', 'player');

        $this->send(
            sprintf('https://webbboly/game/%d', $code),
            $this->twig->render('game/stream/lobby/_player-remove.stream.html.twig', $data)
        );

        $this->send(
            sprintf('https://webbboly/game/%d/player/%s', $code, $player->getNumber()),
            $this->twig->render('game/stream/lobby/_player-kick.stream.html.twig')
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendGameFull(int $code): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d/host', $code),
            $this->twig->render('game/stream/lobby/_game-full.stream.html.twig')
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendGameReady(int $code): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d/host', $code),
            $this->twig->render('game/stream/lobby/_game-ready.stream.html.twig')
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendGameDelete(int $code): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d', $code),
            $this->twig->render('game/stream/lobby/_game-deleted.stream.html.twig')
        );
    }

    private function send(string $topic, string $data): void
    {
        $this->hub->publish(new Update($topic, $data));
    }
}