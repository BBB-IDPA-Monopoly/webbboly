<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\GameBuilding;
use App\Entity\GameField;
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
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function sendUpdateField(Game $game, GameField $field): void
    {
        if ($field instanceof GameBuilding) {
            $this->send(
                sprintf('https://webbboly/game/%d', $game->getCode()),
                $this->twig->render('game/stream/_update-building-field.stream.html.twig',
                    compact('field')
                )
            );
        } else {
            $this->send(
                sprintf('https://webbboly/game/%d', $game->getCode()),
                $this->twig->render('game/stream/_update-action-field.stream.html.twig',
                    compact('field')
                )
            );
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendGameStart(Game $game): void
    {
        $this->send(
            sprintf('https://webbboly/game/%d', $game->getCode()),
            $this->twig->render('game/stream/_game-start.stream.html.twig',
                compact('game')
            )
        );
    }

    private function send(string $topic, string $data): void
    {
        $this->hub->publish(new Update($topic, $data));
    }
}
