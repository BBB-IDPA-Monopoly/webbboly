<?php

namespace App\Service;

use App\Entity\Player;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class GameStreamService
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
            sprintf('/game/%d', $code),
            $this->twig->render('game/stream/lobby/_player-ready.stream.html.twig', [
                ...$data,
                'forHost' => false,
        ]));

        $this->send(
            sprintf('/game/%d/host', $code),
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
    public function sendPlayerReady(int $code, Player $player): void
    {
        $data = compact('code', 'player');

        $this->send(
            sprintf('/game/%d', $code),
            $this->twig->render('game/stream/lobby/_player-ready.stream.html.twig', [
                ...$data,
                'forHost' => false,
        ]));

        $this->send(
            sprintf('/game/%d/host', $code),
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
            sprintf('/game/%d', $code),
            $this->twig->render('game/stream/lobby/_player-leave.stream.html.twig', $data)
        );

        $this->send(
            sprintf('/game/%d/host', $code),
            $this->twig->render('game/stream/lobby/_player-leave.stream.html.twig', $data)
        );
    }

    private function send(string $topic, string $data): void
    {
        $this->hub->publish(new Update($topic, $data));
    }
}
