<?php

namespace App\Service;

use App\Entity\Game;
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
    public function sendPlayerJoin(Game $game, Player $player): void
    {
        $data = compact('player');

        $this->send(
            sprintf('https://webbboly/game/%d', $game->getCode()),
            $this->twig->render('lobby/stream/_player-join.stream.html.twig', [
                ...$data,
                'forHost' => false,
        ]));

        $this->send(
            sprintf('https://webbboly/game/%d/host', $game->getCode()),
            $this->twig->render('lobby/stream/_player-join.stream.html.twig', [
                ...$data,
                'forHost' => true,
        ]));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendPlayerReady(Game $game, Player $player): void
    {
        $data = compact( 'player');

        $this->send(
            sprintf('https://webbboly/game/%d', $game->getCode()),
            $this->twig->render('lobby/stream/_player-ready.stream.html.twig', [
                ...$data,
                'forHost' => false,
        ]));

        $this->send(
            sprintf('https://webbboly/game/%d/host', $game->getCode()),
            $this->twig->render('lobby/stream/_player-ready.stream.html.twig', [
                ...$data,
                'forHost' => true,
        ]));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendPlayerLeave(Game $game, Player $player): void
    {
        $data = compact('player');

        $this->send(
            sprintf('https://webbboly/game/%d', $game->getCode()),
            $this->twig->render('lobby/stream/_player-remove.stream.html.twig', $data)
        );

        $this->send(
            sprintf('https://webbboly/game/%d/player/%s', $game->getCode(), $player->getNumber()),
            $this->twig->render('lobby/stream/_player-leave.stream.html.twig')
        );

        $this->sendUpdateStartLink($game);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendPlayerKick(Game $game, Player $player): void
    {
        $data = compact( 'player');

        $this->send(
            sprintf('https://webbboly/game/%d', $game->getCode()),
            $this->twig->render('lobby/stream/_player-remove.stream.html.twig', $data)
        );

        $this->send(
            sprintf('https://webbboly/game/%d/player/%s', $game->getCode(), $player->getNumber()),
            $this->twig->render('lobby/stream/_player-kick.stream.html.twig')
        );

        $this->sendUpdateStartLink($game);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendUpdateStartLink(Game $game): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d/host', $game->getCode()),
            $this->twig->render('lobby/stream/_game-update-start-link.stream.html.twig',
                compact('game')
            )
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendGameDelete(Game $game): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d', $game->getCode()),
            $this->twig->render('lobby/stream/_game-deleted.stream.html.twig')
        );
    }

    private function send(string $topic, string $data): void
    {
        $this->hub->publish(new Update($topic, $data));
    }
}
